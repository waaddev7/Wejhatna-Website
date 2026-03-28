document.addEventListener('DOMContentLoaded', () => {
    // 1. قاعدة البيانات الكاملة لجميع الرحلات الـ 12 بمعرّفات متطابقة 100%
    const trips = [
        { id: "nablus-groves", subtitle: "Nablus Mountains", title: "Olive Groves", description: "Discover the timeless beauty of ancient olive groves, a symbol of peace and heritage.", date: "October 22, 2023", feedback: [{user: "John D.", comment: "A truly peaceful and historic experience. Highly recommended!"}, {user: "Maria S.", comment: "The tour was amazing."}], image1: "image/NablusOliveGroves.jpg", image2: "image/Palestineoliveseason1.jpeg" },
        { id: "akka-wall", subtitle: "Palestinian Coast", title: "Akka's Wall", description: "A journey through time along the historic city walls overlooking the Mediterranean.", date: "November 5, 2023", feedback: [{user: "Alex P.", comment: "The sea views from the wall are breathtaking."}], image1: "image/akka.jpeg", image2: "image/akka3.png" },
        { id: "holy-sepulchre", subtitle: "Jerusalem", title: "Holy Sepulchre", description: "One of the most sacred Christian sites in the world, located in the heart of the Old City.", date: "September 15, 2023", feedback: [{user: "Emily R.", comment: "A deeply moving and spiritual place."}, {user: "David L.", comment: "An essential visit for anyone traveling to Jerusalem."}], image1: "image/church.JPG", image2: "image/churchh.jpeg" },
        { id: "mountain-summit", subtitle: "Palestinian Highlands", title: "Mountain Summit", description: "Enjoy breathtaking panoramic views from the highest peaks.", date: "July 20, 2023", feedback: [{user: "Chris G.", comment: "The hike was challenging but the view was worth every step."}], image1: "image/mountains (2).jpeg", image2: "image/mountains.png" },
        { id: "traditional-village", subtitle: "Palestinian Countryside", title: "Traditional Village", description: "Wander through the alleys of a traditional village and discover authentic hospitality.", date: "June 12, 2023", feedback: [{user: "Jessica B.", comment: "Felt like stepping back in time. The locals were so welcoming."}], image1: "image/Traditional Village.jpeg", image2: "image/traditionalvillage2.jpeg" },
        { id: "hidden-waterfall", subtitle: "Nature's Treasures", title: "Hidden Waterfall", description: "An adventure to a secluded waterfall nestled among rocks and lush greenery.", date: "May 30, 2023", feedback: [{user: "Tom H.", comment: "A hidden gem! So refreshing and beautiful."}], image1: "image/Hidden Waterfall.jpg", image2: "image/HiddenWaterfall2.jpg" },
        { id: "green-valley", subtitle: "Verdant Valleys", title: "The Green Valley", description: "A peaceful trek through lush valleys, where nature is at its finest.", date: "April 18, 2023", feedback: [{user: "Linda K.", comment: "So green and peaceful. Perfect escape from the city."}], image1: "image/Green Valley.jpg", image2: "image/valley.jpeg" },
        { id: "tiberias-lake", subtitle: "Sea of Galilee", title: "Tiberias Lake", description: "Relax on the shores of the enchanting Sea of Galilee and enjoy its rich history.", date: "March 25, 2023", feedback: [{user: "Mark T.", comment: "Rich in history and incredibly serene."}], image1: "image/Tabaria.png", image2: "image/lakes2.png" },
        { id: "al-aqsa", subtitle: "Jerusalem", title: "Al-Aqsa Mosque", description: "A spiritual experience in the heart of Jerusalem, visiting the blessed Al-Aqsa Mosque.", date: "December 1, 2023", feedback: [{user: "Fatima A.", comment: "The architecture is stunning and the atmosphere is serene."}], image1: "image/quds.jpeg", image2: "image/quds2.jpeg" },
        { id: "jaffa-sea", subtitle: "Jaffa Coast", title: "Jaffa's Seaside", description: "Watch the magical sunset from the ancient and historic port of Jaffa.", date: "February 14, 2024", feedback: [{user: "Michael P.", comment: "The most beautiful sunset I've ever seen."}], image1: "image/jaffa.png", image2: "image/affa.png" },
        { id: "al-masry-palace", subtitle: "Nablus", title: "Munib Al-Masry Palace", description: "A unique architectural masterpiece combining art and history on Mount Gerizim.", date: "January 20, 2024", feedback: [{user: "Sophia L.", comment: "The palace and its gardens are absolutely stunning."}], image1: "image/qasrmunip.png", image2: "image/qasr.png" },
        { id: "hanging-gardens", subtitle: "Haifa", title: "Hanging Gardens", description: "Enjoy the beauty of the magnificent Baha'i Gardens, a masterpiece of architectural landscaping.", date: "August 8, 2023", feedback: [{user: "Sarah K.", comment: "Absolutely pristine and beautiful. A must-see!"}], image1: "image/haifa1.png", image2: "image/haifa2.jpg" },
    ];

    const heroSection = document.querySelector('.hero-section');
    const heroSubtitle = document.querySelector('.hero-subtitle');
    const heroTitle = document.querySelector('.hero-title');
    // ... (باقي الكود لا يحتاج أي تغيير)

    const heroDescription = document.querySelector('.hero-description');
    const tripDateElement = document.querySelector('.trip-date');
    const feedbackListElement = document.querySelector('.feedback-list');
    const sliderContainer = document.querySelector('.slider-container');
    const currentSlideNumber = document.getElementById('current-slide-number');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    let currentIndex = 0;

    const urlParams = new URLSearchParams(window.location.search);
    const tripIdFromUrl = urlParams.get('trip');

    if (tripIdFromUrl) {
        const foundIndex = trips.findIndex(trip => trip.id === tripIdFromUrl);
        if (foundIndex !== -1) {
            currentIndex = foundIndex;
        }
    }

    function createTripCards() {
        sliderContainer.innerHTML = '';
        const activeTrip = trips[currentIndex];
        const card1 = document.createElement('div'), card2 = document.createElement('div');
        card1.classList.add('trip-card');
        card1.innerHTML = `<img src="${activeTrip.image1}" alt="${activeTrip.title}"><div class="card-overlay"><h3 class="card-title">${activeTrip.title}</h3></div>`;
        card2.classList.add('trip-card');
        card2.innerHTML = `<img src="${activeTrip.image2}" alt="${activeTrip.title}"><div class="card-overlay"><h3 class="card-title">${activeTrip.title} (Alt. View)</h3></div>`;

        card1.addEventListener('click', () => {
            heroSection.style.backgroundImage = `url('${activeTrip.image1}')`;
            card1.classList.add('active'); card2.classList.remove('active');
        });
        card2.addEventListener('click', () => {
            heroSection.style.backgroundImage = `url('${activeTrip.image2}')`;
            card2.classList.add('active'); card1.classList.remove('active');
        });

        sliderContainer.appendChild(card1);
        sliderContainer.appendChild(card2);

        if (heroSection.style.backgroundImage.includes(activeTrip.image1)) {
            card1.classList.add('active');
        } else {
            card2.classList.add('active');
        }
    }

    function updateUI() {
        const activeTrip = trips[currentIndex];
        heroSubtitle.textContent = activeTrip.subtitle;
        heroTitle.textContent = activeTrip.title;
        heroDescription.textContent = activeTrip.description;
        tripDateElement.textContent = `Trip Date: ${activeTrip.date}`;

        feedbackListElement.innerHTML = '';
        if (activeTrip.feedback && activeTrip.feedback.length > 0) {
            activeTrip.feedback.forEach(fb => {
                const feedbackItem = document.createElement('div');
                feedbackItem.classList.add('feedback-item');
                feedbackItem.innerHTML = `<p>"${fb.comment}"</p><span>- ${fb.user}</span>`;
                feedbackListElement.appendChild(feedbackItem);
            });
        } else {
            feedbackListElement.innerHTML = `<p>No feedback yet for this trip.</p>`;
        }

        heroSection.style.backgroundImage = `url('${activeTrip.image2}')`;
        currentSlideNumber.textContent = `0${(currentIndex % trips.length) + 1}`;
        createTripCards();
    }

    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % trips.length;
        updateUI();
    });
    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + trips.length) % trips.length;
        updateUI();
    });

    updateUI();
});