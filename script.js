// ```javascript
// // =========================
// // CART
// // =========================

// let cart = [];


// // เพิ่มสินค้าเข้าตะกร้า
// function addToCart(name, price) {

//     const product = {
//         name: name,
//         price: price
//     };

//     cart.push(product);

//     updateCart();

//     alert(name + " ถูกเพิ่มลงตะกร้าแล้ว!");
// }


// // อัปเดตจำนวนสินค้า
// function updateCart() {

//     document.getElementById("cartCount").textContent = cart.length;

//     const cartItems = document.getElementById("cartItems");
//     const cartTotal = document.getElementById("cartTotal");

//     if (cart.length === 0) {

//         cartItems.innerHTML =
//             '<p class="empty-cart">ยังไม่มีสินค้าในตะกร้า</p>';

//         cartTotal.textContent = "฿0";

//         return;
//     }


//     let total = 0;

//     cartItems.innerHTML = "";


//     cart.forEach((item, index) => {

//         total += item.price;

//         const itemElement = document.createElement("div");

//         itemElement.classList.add("cart-item");

//         itemElement.innerHTML = `
//             <span>${item.name}</span>

//             <span>
//                 ฿${item.price.toLocaleString()}
//                 <button onclick="removeFromCart(${index})">
//                     ❌
//                 </button>
//             </span>
//         `;

//         cartItems.appendChild(itemElement);

//     });


//     cartTotal.textContent =
//         "฿" + total.toLocaleString();
// }


// // ลบสินค้า
// function removeFromCart(index) {

//     cart.splice(index, 1);

//     updateCart();
// }


// // เปิดตะกร้า
// function openCart() {

//     document.getElementById("cartModal").style.display = "flex";

// }


// // ปิดตะกร้า
// function closeCart() {

//     document.getElementById("cartModal").style.display = "none";

// }


// // =========================
// // SEARCH
// // =========================

// function searchGames() {

//     const search =
//         document
//             .getElementById("searchInput")
//             .value
//             .toLowerCase();


//     const games =
//         document.querySelectorAll(".game-card");


//     games.forEach(game => {

//         const name =
//             game.dataset.name.toLowerCase();


//         if (name.includes(search)) {

//             game.style.display = "block";

//         } else {

//             game.style.display = "none";

//         }

//     });

// }


// // =========================
// // CATEGORY FILTER
// // =========================

// function filterGames(category) {

//     const games =
//         document.querySelectorAll(".game-card");


//     games.forEach(game => {

//         if (
//             category === "all" ||
//             game.dataset.category === category
//         ) {

//             game.style.display = "block";

//         } else {

//             game.style.display = "none";

//         }

//     });

// }


// // =========================
// // SCROLL
// // =========================

// function scrollToGames() {

//     document
//         .getElementById("games")
//         .scrollIntoView({
//             behavior: "smooth"
//         });

// }


// // =========================
// // CHECKOUT
// // =========================

// function checkout() {

//     if (cart.length === 0) {

//         alert("ยังไม่มีสินค้าในตะกร้า");

//         return;
//     }


//     alert(
//         "ขอบคุณสำหรับการสั่งซื้อ! 🎮"
//     );

// }
// ```
