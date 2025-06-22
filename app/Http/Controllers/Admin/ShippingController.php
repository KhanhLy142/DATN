<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Shipping::with('order');

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Lọc theo phương thức vận chuyển
        if ($request->filled('shipping_method')) {
            $query->byMethod($request->shipping_method);
        }

        // Lọc theo trạng thái
        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $request->shipping_status);
        }

        // Lọc theo tỉnh/thành phố
        if ($request->filled('province')) {
            $query->byProvince($request->province);
        }

        // Sắp xếp mới nhất trước
        $query->orderBy('created_at', 'desc');

        // Phân trang
        $shippings = $query->paginate(15)->withQueryString();

        // Thống kê
        $stats = Shipping::getStatistics();

        return view('admin.shippings.index', compact('shippings', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lấy danh sách đơn hàng chưa có thông tin vận chuyển
        $orders = Order::whereDoesntHave('shipping')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.shippings.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id|unique:shippings,order_id',
            'shipping_address' => 'required|string|max:1000',
            'shipping_method' => 'required|in:standard,express',
            'shipping_status' => 'required|in:pending,shipped,delivered',
            'province' => 'nullable|string|max:255',
            'shipping_fee' => 'nullable|numeric|min:0',
            'shipping_note' => 'nullable|string|max:1000',
            'tracking_code' => 'nullable|string|max:100|unique:shippings,tracking_code'
        ], [
            'order_id.required' => 'Vui lòng chọn đơn hàng',
            'order_id.unique' => 'Đơn hàng này đã có thông tin vận chuyển',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'shipping_method.required' => 'Vui lòng chọn phương thức vận chuyển',
            'shipping_status.required' => 'Vui lòng chọn trạng thái vận chuyển',
            'tracking_code.unique' => 'Mã vận đơn đã tồn tại'
        ]);

        try {
            DB::beginTransaction();

            $shipping = Shipping::create([
                'order_id' => $request->order_id,
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'shipping_status' => $request->shipping_status,
                'province' => $request->province,
                'shipping_fee' => $request->shipping_fee ?? 0,
                'shipping_note' => $request->shipping_note,
                'tracking_code' => $request->tracking_code
            ]);

            // Cập nhật trạng thái đơn hàng
            $order = Order::find($request->order_id);
            if ($order && $request->shipping_status === 'shipped') {
                $order->update(['status' => 'shipped']);
            } elseif ($order && $request->shipping_status === 'delivered') {
                $order->update(['status' => 'completed']);
            }

            DB::commit();

            return redirect()->route('admin.shippings.index')
                ->with('success', 'Tạo thông tin vận chuyển thành công!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipping $shipping)
    {
        $shipping->load('order');

        return view('admin.shippings.show', compact('shipping'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipping $shipping)
    {
        $shipping->load('order');

        return view('admin.shippings.edit', compact('shipping'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipping $shipping)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:1000',
            'shipping_method' => 'required|in:standard,express',
            'shipping_status' => 'required|in:pending,shipped,delivered',
            'province' => 'nullable|string|max:255',
            'shipping_fee' => 'nullable|numeric|min:0',
            'shipping_note' => 'nullable|string|max:1000',
            'tracking_code' => 'nullable|string|max:100|unique:shippings,tracking_code,' . $shipping->id
        ], [
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'shipping_method.required' => 'Vui lòng chọn phương thức vận chuyển',
            'shipping_status.required' => 'Vui lòng chọn trạng thái vận chuyển',
            'tracking_code.unique' => 'Mã vận đơn đã tồn tại'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $shipping->shipping_status;

            $shipping->update([
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'shipping_status' => $request->shipping_status,
                'province' => $request->province,
                'shipping_fee' => $request->shipping_fee ?? 0,
                'shipping_note' => $request->shipping_note,
                'tracking_code' => $request->tracking_code
            ]);

            // Cập nhật trạng thái đơn hàng nếu thay đổi trạng thái vận chuyển
            if ($oldStatus !== $request->shipping_status && $shipping->order) {
                if ($request->shipping_status === 'shipped') {
                    $shipping->order->update(['status' => 'shipped']);
                } elseif ($request->shipping_status === 'delivered') {
                    $shipping->order->update(['status' => 'completed']);
                }
            }

            DB::commit();

            // Redirect về trang danh sách thay vì trang chi tiết
            return redirect()->route('admin.shippings.index')
                ->with('success', 'Cập nhật thông tin vận chuyển thành công!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipping $shipping)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem có thể xóa không
            if ($shipping->shipping_status === 'delivered') {
                return redirect()->back()
                    ->with('error', 'Không thể xóa thông tin vận chuyển đã hoàn thành!');
            }

            $shipping->delete();

            DB::commit();

            return redirect()->route('admin.shippings.index')
                ->with('success', 'Xóa thông tin vận chuyển thành công!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu đang giao hàng
     */
    public function markAsShipped(Request $request, Shipping $shipping)
    {
        $request->validate([
            'tracking_code' => 'nullable|string|max:100|unique:shippings,tracking_code,' . $shipping->id
        ]);

        try {
            $shipping->markAsShipped($request->tracking_code);

            return redirect()->back()
                ->with('success', 'Đã cập nhật trạng thái đang giao hàng!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu đã giao hàng
     */
    public function markAsDelivered(Shipping $shipping)
    {
        try {
            $shipping->markAsDelivered();

            return redirect()->back()
                ->with('success', 'Đã cập nhật trạng thái giao hàng thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Tạo mã vận đơn tự động
     */
    public function generateTrackingCode(Shipping $shipping)
    {
        try {
            $trackingCode = $shipping->generateTrackingCode();

            return response()->json([
                'success' => true,
                'tracking_code' => $trackingCode,
                'message' => 'Tạo mã vận đơn thành công!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xuất báo cáo vận chuyển
     */
    public function export(Request $request)
    {
        // Logic xuất Excel/PDF có thể thêm sau
        return redirect()->back()
            ->with('info', 'Chức năng xuất báo cáo đang được phát triển!');
    }

    /**
     * Thống kê vận chuyển
     */
    public function statistics()
    {
        $stats = Shipping::getStatistics();
        $methodStats = Shipping::getMethodStatistics();

        // Thống kê theo tháng
        $monthlyStats = Shipping::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total,
                SUM(shipping_fee) as total_fee
            ')
            ->whereYear('created_at', date('Y'))
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        return view('admin.shippings.statistics', compact('stats', 'methodStats', 'monthlyStats'));
    }

    /**
     * API lấy thông tin vận chuyển theo đơn hàng
     */
    public function getByOrder($orderId)
    {
        $shipping = Shipping::where('order_id', $orderId)->first();

        if (!$shipping) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin vận chuyển'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shipping
        ]);
    }
}
