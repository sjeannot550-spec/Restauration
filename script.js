let menu = document.querySelector('#menu-bars');
let navbar = document.querySelector('.navbar');

menu.onclick = () =>{
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
}

let section = document.querySelectorAll('section');
let navLinks = document.querySelectorAll('header .navbar a');

window.onscroll = () =>{
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');

    section.forEach(sec =>{

        let top = window.scrollY;
        let height = sec.offsetHeight;
        let offset = offseTop - 150;
        let id = sec.getAttribute('id');

        if(top => offset && top < offset + height){
            navLinks.forEach(links => {
                links.classList.remove('active');
                document.querySelector('header .navbar a[href*='+id+']').classList.add('active');
            })
        }

    });
}

document.querySelector('#search-icons').onclick = () =>{
    document.querySelector('#search-form').classList.toggle('active');
}

document.querySelector('#close').onclick = () =>{
    document.querySelector('#search-form').classList.remove('active');
}

var swiper = new Swiper(".home-slider", {
      spaceBetween: 30,
      centeredSlides: true,
      autoplay: {
        delay: 7500,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      loop:true,
    });

    var swiper = new Swiper(".Review-slider", {
      spaceBetween: 20,
      centeredSlides: true,
      autoplay: {
        delay: 7500,
        disableOnInteraction: false,
      },
      loop:true,
      breakpoints: {
        0: {
            slidesPerView: 1,
        },
        640: {
          slidesPerView: 2,
        },
        768: {
          slidesPerView: 3,
        },
        1024: {
          slidesPerView: 4,
        },
      },
    });

    function loader() {
    document.querySelector('.loader-container').classList.add('fade-out');
    }

    function fadeOut() {
    // On utilise setTimeout au lieu de setInterval
    setTimeout(loader, 3000);
    }

    window.onload = fadeOut;

    // --- GESTION DU PANIER ---
let cart = [];
const cartIcon = document.querySelector('.fa-shopping-cart');

// 1. Écouter les clics sur tous les boutons "add to cart"
document.querySelectorAll('.btn').forEach(button => {
    if (button.textContent.trim().toLowerCase() === "add to cart") {
        button.onclick = (e) => {
            e.preventDefault();
            // Récupération des données depuis les attributs data-
            const name = button.getAttribute('data-name');
            const price = parseFloat(button.getAttribute('data-price'));

            if (name && !isNaN(price)) {
                addToCart(name, price);
            } else {
                alert("Erreur: Données du produit manquantes sur ce bouton.");
            }
        };
    }
});

function addToCart(name, price) {
    const existingItem = cart.find(item => item.name === name);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ name, price, quantity: 1 });
    }
    updateCartUI();
}

function updateCartUI() {
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);

    // Mise à jour visuelle de l'icône panier
    cartIcon.innerHTML = `<i class="fas fa-shopping-cart"></i><span style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; padding:2px 6px; font-size:1.2rem;">${itemCount}</span>`;
    cartIcon.style.position = 'relative';

    // Remplissage automatique du formulaire "Order Now"
    const orderInput = document.querySelector('input[placeholder="enter food name"]');
    const additionalInput = document.querySelector('input[placeholder="extra with food"]');

    if (orderInput) {
        orderInput.value = cart.map(item => `${item.quantity}x ${item.name}`).join(', ');
    }
    if (additionalInput) {
        additionalInput.value = `TOTAL: $${total.toFixed(2)}`;
    }
}

// --- GESTION DE LA COMMANDE ET DE LA FACTURE MAGNIFIQUE ---
const orderForm = document.querySelector('#Order form');
const modal = document.getElementById('invoice-modal');
const invoiceBody = document.getElementById('invoice-body');
const closeInvoiceBtn = document.getElementById('close-invoice');

orderForm.onsubmit = (e) => {
    e.preventDefault();

    if (cart.length === 0) {
        alert("Votre panier est vide !");
        return;
    }

    // Récupération des données
    const clientName = orderForm.querySelector('input[placeholder="enter your name"]').value;
    const clientPhone = orderForm.querySelector('input[placeholder="enter your number"]').value;
    const clientAddress = orderForm.querySelector('textarea[placeholder="enter your address"]').value;
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    // Préparation du texte pour WhatsApp
    let detailWhatsApp = cart.map(item => `- ${item.name} (x${item.quantity})`).join('%0A');
    const msgWhatsApp = `*NOUVELLE COMMANDE*%0A*Client:* ${clientName}%0A*Total:* $${total.toFixed(2)}%0A*Détails:*%0A${detailWhatsApp}`;

    // Injection de la facture dans la MODALE
    invoiceBody.innerHTML = `
        <p><b>Client :</b> ${clientName}</p>
        <p><b>Téléphone :</b> ${clientPhone}</p>
        <p><b>Adresse :</b> ${clientAddress}</p>
        <hr style="margin:1rem 0; border:0; border-top:1px solid #eee;">
        <p><b>Articles :</b></p>
        <ul style="list-style:none;">
            ${cart.map(item => `<li>${item.quantity}x ${item.name} - $${(item.price * item.quantity).toFixed(2)}</li>`).join('')}
        </ul>
        <h3 style="margin-top:1rem; color:var(--green); font-size:2rem;">Total à payer : $${total.toFixed(2)}</h3>
    `;

    // Afficher la modale
    modal.style.display = 'flex';

    // Envoyer aussi vers WhatsApp en arrière-plan (optionnel)
    window.open(`https://wa.me/243829134460?text=${msgWhatsApp}`, '_blank');
};

// Quand on clique sur le bouton de la facture
closeInvoiceBtn.onclick = () => {
    // 1. Cacher la modale
    modal.style.display = 'none';
    
    // 2. Afficher le formulaire de feedback
    const feedbackSection = document.getElementById('Feedback');
    feedbackSection.style.display = 'block';
    
    // 3. Faire défiler jusqu'au formulaire d'avis
    window.location.hash = "Feedback";

    // 4. Vider le panier
    cart = [];
    updateCartUI();
    orderForm.reset();
};

const feedbackForm = document.getElementById('feedback-form');

feedbackForm.onsubmit = (e) => {
    e.preventDefault();

    // Récupération du nom et du commentaire
    const clientName = document.getElementById('user-name').value || "Client Anonyme";
    const comment = document.getElementById('user-comment').value;
    const rating = document.getElementById('user-rating').value;
    const photoInput = document.getElementById('user-photo');

    // Gestion de la photo
    let photoURL = "images/pic-1.png"; // Image par défaut
    if (photoInput.files && photoInput.files[0]) {
        photoURL = URL.createObjectURL(photoInput.files[0]);
    }

    // Étoiles
    let starsHTML = '';
    for(let i=0; i<5; i++) {
        starsHTML += i < rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
    }

    const swiperWrapper = document.querySelector('.Review-slider .swiper-wrapper');
    const newReview = document.createElement('div');
    newReview.classList.add('swiper-slide', 'slide');

    // Structure conforme à votre deuxième image
    newReview.innerHTML = `
        <i class="fas fa-quote-right"></i>
        <div class="user">
            <img src="${photoURL}" alt="">
            <div class="user-info">
                <h3>${clientName}</h3>
                <div class="stars">${starsHTML}</div>
            </div>
        </div>
        <p>${comment}</p>
    `;

    // Ajouter au début
    swiperWrapper.prepend(newReview);
    
    // Rafraîchir Swiper pour prendre en compte le nouveau slide et les marges
    if(typeof swiper !== 'undefined') {
        // Selon votre configuration, c'est souvent le deuxième Swiper (index 1)
        if(Array.isArray(swiper)) {
            swiper[1].update();
        } else {
            // Si vous n'avez qu'un seul objet swiper global pour les reviews
            var reviewSwiper = new Swiper(".Review-slider", {
                spaceBetween: 20, // Ajoute de l'espace entre les slides
                centeredSlides: true,
                autoplay: { delay: 7500, disableOnInteraction: false },
                loop: true,
                breakpoints: {
                    0: { slidesPerView: 1 },
                    640: { slidesPerView: 2 },
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                },
            });
        }
    }

    alert("Merci " + clientName + " ! Votre avis a été publié.");
    feedbackForm.reset();
    document.getElementById('Feedback').style.display = 'none';
};