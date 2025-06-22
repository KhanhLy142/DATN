// ---------------------------------------------
// Custom Scripts for Beautiec Cosmetic Website
// ---------------------------------------------

// Auto dropdown on hover (account + cart)
document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll('.position-relative');

    dropdowns.forEach(function (dropdown) {
        dropdown.addEventListener('mouseenter', function () {
            const child = this.querySelector('.account-dropdown, .cart-dropdown');
            if (child) {
                child.style.display = 'flex';
            }
        });

        dropdown.addEventListener('mouseleave', function () {
            const child = this.querySelector('.account-dropdown, .cart-dropdown');
            if (child) {
                child.style.display = 'none';
            }
        });
    });
});

// Scroll to top button (optional nếu bạn thích)
const scrollTopButton = document.createElement('button');
scrollTopButton.innerHTML = '⬆️';
scrollTopButton.style.position = 'fixed';
scrollTopButton.style.bottom = '20px';
scrollTopButton.style.right = '20px';
scrollTopButton.style.display = 'none';
scrollTopButton.style.background = '#f8b6c1';
scrollTopButton.style.border = 'none';
scrollTopButton.style.padding = '10px';
scrollTopButton.style.borderRadius = '50%';
scrollTopButton.style.zIndex = '999';
document.body.appendChild(scrollTopButton);

window.addEventListener('scroll', function () {
    if (window.scrollY > 300) {
        scrollTopButton.style.display = 'block';
    } else {
        scrollTopButton.style.display = 'none';
    }
});

scrollTopButton.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

document.addEventListener('DOMContentLoaded', function () {
    const items = document.querySelectorAll('.dropdown-submenu .dropdown-item');

    items.forEach(function (item) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            items.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

