<?php
// ================================================================
// CONTROLLERS - request handling + validation + page loading
// ================================================================

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/* ============== Login for local testing ============== */
function loginCtrl($conn) {
    $error = '';
    $oldEmail = $_COOKIE['remember_email'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if ($email === '' || $password === '') {
            $error = 'Email and password are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $user = authUser($conn, $email, $password);
            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'is_verified' => $user['is_verified']
                ];
                if ($remember) setcookie('remember_email', $email, time() + 86400 * 30, '/');
                else setcookie('remember_email', '', time() - 3600, '/');
                header('Location: index.php?page=browse');
                exit;
            }
            $error = 'Invalid email or password.';
        }
    }

    require 'views/login.php';
}

function browseCtrl($conn) {
    $posts = getApprovedPosts($conn);
    $countries = getCountries($conn);
    $genres = getGenres($conn);
    $featuredDests = getFeaturedDestinations();
    require 'views/browse.php';
}

/* ============== Detail Page ============== */
function detailCtrl($conn) {
    $id = intval($_GET['id'] ?? 0);
    $post = getPostById($conn, $id);

    if (!$post) {
        header('Location: index.php?page=browse&msg=notfound');
        exit;
    }

    $comments = getCommentsByPost($conn, $id);
    $cost = getBaseCost($conn, $id, $post['cost_level']);
    require 'views/detail.php';
}

/* ============== AJAX: Search/Filter/Add Comment/Delete Comment ============== */
function ajaxCtrl($conn) {
    $type = $_GET['type'] ?? '';

    if ($type === 'search') {
        $q = trim($_GET['q'] ?? '');
        jsonResponse(['success' => true, 'posts' => searchPosts($conn, $q)]);
    }

    if ($type === 'filter') {
        $country = trim($_GET['country'] ?? '');
        $genre = trim($_GET['genre'] ?? '');
        $cost = trim($_GET['cost'] ?? '');
        $allowedCosts = ['', 'low', 'medium', 'high'];
        $allowedGenres = ['beach', 'mountain', 'city', 'historical'];

        if (!in_array($cost, $allowedCosts, true)) {
            jsonResponse(['success' => false, 'message' => 'Invalid cost level.'], 400);
        }

        $genres = [];
        if ($genre !== '') {
            foreach (explode(',', $genre) as $g) {
                $g = trim($g);
                if (in_array($g, $allowedGenres, true)) $genres[] = $g;
            }
        }

        jsonResponse(['success' => true, 'posts' => filterPosts($conn, $country, $genres, $cost)]);
    }

    if ($type === 'add_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isVerifiedGeneralUser()) {
            jsonResponse(['success' => false, 'message' => 'Only verified general users can comment.'], 403);
        }

        $postId = intval($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($postId <= 0 || $content === '') {
            jsonResponse(['success' => false, 'message' => 'Comment cannot be empty.'], 422);
        }
        if (strlen($content) > 500) {
            jsonResponse(['success' => false, 'message' => 'Comment must be within 500 characters.'], 422);
        }
        if (!getPostById($conn, $postId)) {
            jsonResponse(['success' => false, 'message' => 'Post not found.'], 404);
        }

        $newId = addComment($conn, $postId, $_SESSION['user']['id'], $content);
        jsonResponse([
            'success' => true,
            'comment' => [
                'id' => $newId,
                'name' => $_SESSION['user']['name'],
                'content' => e($content),
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    if ($type === 'delete_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isVerifiedGeneralUser()) {
            jsonResponse(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $commentId = intval($_POST['comment_id'] ?? 0);
        if ($commentId <= 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid comment ID.'], 422);
        }

        if (deleteOwnComment($conn, $commentId, $_SESSION['user']['id'])) {
            jsonResponse(['success' => true, 'message' => 'Comment deleted.']);
        }
        jsonResponse(['success' => false, 'message' => 'Comment not found or not yours.'], 404);
    }

    jsonResponse(['success' => false, 'message' => 'Invalid AJAX request.'], 400);
}

/* ============== Featured Static Destinations ============== */
function getFeaturedDestinations()
{
    return [
        /* 0 */ ['title' => 'Taj Mahal, Agra',            'country' => 'India',     'genre' => 'historical', 'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'One of the Seven Wonders of the World, this ivory-white marble mausoleum was built by Mughal emperor Shah Jahan in memory of his beloved wife Mumtaz Mahal. Set on the banks of the Yamuna River in Agra, the complex spans 17 hectares and took 22 years to complete. The Taj Mahal is renowned for its perfect symmetry, intricate inlay work, and the way it changes colour with the light throughout the day.', 'travel_medium_info' => 'Best reached by train from Delhi (2-3 hrs). Nearest airport is Agra Airport. Ideal months: Oct-Mar.'],
        /* 1 */ ['title' => 'Goa Beaches',                'country' => 'India',     'genre' => 'beach',      'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'Famous for its stunning coastline, vibrant nightlife, and Portuguese-influenced architecture, Goa is India\'s most beloved beach destination with over 100 km of pristine shores. From the lively Baga and Calangute beaches to the tranquil Palolem and Agonda in the south, each beach has its own personality. Explore spice plantations, colonial churches, and a unique blend of Indian and European cultures.', 'travel_medium_info' => 'Goa International Airport connects to major Indian cities. Trains from Mumbai take 8-12 hrs. Best season: Nov-Feb.'],
        /* 2 */ ['title' => 'Kerala Backwaters',          'country' => 'India',     'genre' => 'beach',      'cost_level' => 'low',    'base_cost' => 500,  'short_history' => 'A network of interconnected canals, rivers, lakes, and inlets formed by more than 900 km of waterways stretching across Kerala. Overnight houseboat cruises through lush backwaters, paddy fields, and coconut groves offer a uniquely serene experience. Alappuzha (Alleppey) is the main hub; the famous Nehru Trophy Snake Boat Race takes place here every August.', 'travel_medium_info' => 'Fly into Cochin or Trivandrum airport. Houseboat rentals depart from Alleppey (Alappuzha). Best visited Oct-Feb.'],
        /* 3 */ ['title' => 'Jaipur — The Pink City',     'country' => 'India',     'genre' => 'historical', 'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'The capital of Rajasthan, known for its stunning palaces, forts, and vibrant bazaars. The Amber Fort, Hawa Mahal (Palace of Winds), City Palace, and Jantar Mantar observatory make it a treasure trove of Rajput architecture. The entire old city is painted terracotta pink — a tradition dating back to 1876 when the city was painted for Prince Albert\'s visit.', 'travel_medium_info' => 'Jaipur International Airport is well connected. Trains from Delhi take 4-5 hrs. Part of the Golden Triangle tourist circuit.'],
        /* 4 */ ['title' => 'Manali, Himachal Pradesh',   'country' => 'India',     'genre' => 'mountain',   'cost_level' => 'low',    'base_cost' => 500,  'short_history' => 'A high-altitude Himalayan resort town at 2,050 m, known for adventure sports including skiing, paragliding, river rafting, and trekking. Surrounded by snow-capped peaks, apple orchards, and dense cedar forests, Manali is also home to the ancient Hadimba Devi Temple. The Rohtang Pass (3,978 m) offers dramatic views and snow even in summer.', 'travel_medium_info' => 'Nearest airport is Bhuntar (Kullu). Volvo bus from Delhi takes 14-16 hrs. Best season: Mar-Jun for treks; Dec-Jan for snow.'],
        /* 5 */ ['title' => 'Sigiriya Rock Fortress',     'country' => 'Sri Lanka', 'genre' => 'historical', 'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'An ancient rock fortress and palace ruin rising nearly 200 m above the surrounding plains. Built by King Kashyapa I in the 5th century AD, it features remarkable frescoes of celestial maidens, a mirror wall with ancient graffiti, landscaped water gardens, and a lion-paw entrance staircase. Now a UNESCO World Heritage Site and one of Sri Lanka\'s most visited monuments.', 'travel_medium_info' => 'Fly into Colombo Bandaranaike Airport then drive 4-5 hrs. Nearest town is Dambulla. Best visited during dry season (May-Sep).'],
        /* 6 */ ['title' => 'Mirissa Beach',              'country' => 'Sri Lanka', 'genre' => 'beach',      'cost_level' => 'low',    'base_cost' => 500,  'short_history' => 'A picturesque crescent-shaped bay on the southern coast of Sri Lanka, famous for its relaxed atmosphere and turquoise waters. Mirissa is one of the best places in the world for blue whale watching (Dec-Apr). Surfers, backpackers, and couples alike love the palm-fringed beach, fresh seafood, and stunning sunsets. Coconut Tree Hill viewpoint offers a memorable panoramic photograph.', 'travel_medium_info' => 'Drive 3 hrs south from Colombo. Nearest town is Weligama. Blue whale watching tours run Dec-Apr. Best surf season Nov-Apr.'],
        /* 7 */ ['title' => 'Ella Hill Country',          'country' => 'Sri Lanka', 'genre' => 'mountain',   'cost_level' => 'low',    'base_cost' => 500,  'short_history' => 'A small town nestled in the green hills of Sri Lanka\'s highland tea country at around 1,000 m elevation. Ella is famous for the iconic Nine Arches Bridge, the hike up Little Adam\'s Peak, Ella Rock, and sweeping views over Ravana Falls. The area is blanketed in lush tea plantations; visitors can tour a tea factory and sample fresh Ceylon tea.', 'travel_medium_info' => 'Take the scenic train from Kandy (6-7 hrs) — one of the world\'s most beautiful rail journeys. Best visited Mar-Aug.'],
        /* 8 */ ['title' => 'Colombo',                    'country' => 'Sri Lanka', 'genre' => 'city',       'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'Sri Lanka\'s bustling commercial capital blends colonial heritage with a rapidly modernising skyline. The Pettah Bazaar is a sensory feast of spices, fabrics, and street food. Key highlights include the National Museum, Gangaramaya Temple, the scenic Galle Face Green promenade, and the vibrant Colombo 07 neighbourhood with galleries, boutiques, and cafes.', 'travel_medium_info' => 'Bandaranaike International Airport is 30 km north of the city. Colombo is the main entry point for Sri Lanka. Well-connected by bus and train.'],
        /* 9 */ ['title' => 'Temple of the Tooth, Kandy', 'country' => 'Sri Lanka', 'genre' => 'historical', 'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'Sri Dalada Maligawa, the Temple of the Sacred Tooth Relic, is a royal palace complex in the hill city of Kandy that houses Buddhism\'s most venerated relic — a tooth of the historical Buddha. The temple sits beside the picturesque Kandy Lake, within the last royal capital of Sri Lanka. The annual Esala Perahera festival features elaborately decorated elephants, fire dancers, and drummers.', 'travel_medium_info' => 'Kandy is 115 km from Colombo (3-4 hrs by train or bus). Best visited during the Esala Perahera festival (Jul-Aug).'],
        /* 10 */ ['title' => 'Everest Base Camp',         'country' => 'Nepal',     'genre' => 'mountain',   'cost_level' => 'high',   'base_cost' => 3000, 'short_history' => 'The ultimate trekking challenge, Everest Base Camp (5,364 m) draws thousands of adventurers each year to the foot of the world\'s highest peak. The classic Khumbu route passes through Namche Bazaar, Tengboche Monastery, Dingboche, and Gorak Shep before reaching base camp. Along the way trekkers experience dramatic glacier views, Sherpa culture, yak caravans, and breathtaking high-altitude scenery.', 'travel_medium_info' => 'Fly Kathmandu to Lukla (35 min) then 12-14 day trek to base camp at 5,364 m. Best seasons: Mar-May and Sep-Nov. Permits required.'],
        /* 11 */ ['title' => 'Kathmandu Valley',          'country' => 'Nepal',     'genre' => 'historical', 'cost_level' => 'low',    'base_cost' => 500,  'short_history' => 'A UNESCO World Heritage valley containing seven monument zones, including Pashupatinath Temple (one of Hinduism\'s holiest sites), Boudhanath Stupa (the world\'s largest), Swayambhunath (Monkey Temple), and the medieval royal squares of Kathmandu, Patan, and Bhaktapur. The valley is the cultural heart of Nepal, blending Hindu and Buddhist traditions with remarkable art and architecture.', 'travel_medium_info' => 'Tribhuvan International Airport is in Kathmandu. Good road and air connections across Nepal. Best visited Oct-Nov and Mar-Apr.'],
        /* 12 */ ['title' => 'Pokhara',                   'country' => 'Nepal',     'genre' => 'mountain',   'cost_level' => 'low',    'base_cost' => 500,  'short_history' => 'Nepal\'s adventure capital sits at 820 m on the shores of the serene Phewa Lake, with the dramatic Annapurna massif and Machhapuchhre (Fish Tail) as a stunning backdrop. Pokhara is famous for paragliding, ultra-light flights, zip-lining, kayaking, and being the starting point of the Annapurna Circuit and Sanctuary treks. The Lakeside area has a relaxed, cosmopolitan vibe with mountain views.', 'travel_medium_info' => '25-min flight or 7-hr bus from Kathmandu. Pokhara is the gateway to Annapurna treks. Best seasons: Oct-Nov and Mar-Apr.'],
        /* 13 */ ['title' => 'Chitwan National Park',     'country' => 'Nepal',     'genre' => 'mountain',   'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'Nepal\'s first national park and a UNESCO World Heritage Site, covering 952 sq km of subtropical lowland forest and grassland. Home to the endangered one-horned rhinoceros, Bengal tiger, gharial crocodile, and over 600 bird species. Activities include jeep safaris, canoe rides, jungle walks, elephant bathing, and visits to the Tharu cultural village. One of Asia\'s best wildlife destinations.', 'travel_medium_info' => 'Drive 5-6 hrs from Kathmandu or take a short flight to Bharatpur. Safaris depart from Sauraha village. Best season: Oct-Mar.'],
        /* 14 */ ['title' => 'Annapurna Circuit',         'country' => 'Nepal',     'genre' => 'mountain',   'cost_level' => 'medium', 'base_cost' => 1500, 'short_history' => 'Considered one of the world\'s greatest trekking routes, the Annapurna Circuit circumnavigates the Annapurna massif through diverse landscapes — subtropical forests, rice terraces, alpine meadows, the arid trans-Himalayan zone of Mustang, and glaciated high peaks. Highlights include the Thorong La pass (5,416 m), Muktinath Temple, and the ancient town of Manang.', 'travel_medium_info' => 'Start from Besisahar (5 hrs from Kathmandu by bus). Full circuit takes 12-21 days. Thorong La pass at 5,416 m. Permits required.'],
        /* 15 */ ['title' => 'Paris — City of Light',     'country' => 'France',    'genre' => 'city',       'cost_level' => 'high',   'base_cost' => 3000, 'short_history' => 'The iconic French capital is home to the Eiffel Tower, the Louvre Museum (world\'s largest art museum), Notre-Dame Cathedral, the Musee d\'Orsay, and the Champs-Elysees. Stroll along the Seine, explore the Marais district, and experience world-class cuisine and fashion. Paris hosts 30 million visitors per year and is consistently voted the world\'s most visited city.', 'travel_medium_info' => 'Charles de Gaulle Airport is the main international hub. Excellent metro and RER network within the city. Eurostar connects London in 2.5 hrs. Best visited spring (Apr-Jun) or autumn (Sep-Oct).'],
        /* 16 */ ['title' => 'French Riviera (Cote d\'Azur)', 'country' => 'France', 'genre' => 'beach',    'cost_level' => 'high',   'base_cost' => 3000, 'short_history' => 'The glamorous Mediterranean coastline stretching from Menton near the Italian border to Toulon, encompassing Nice, Cannes, Antibes, Saint-Tropez, and Monaco. Famous for its azure waters, luxury yachts, pebble beaches, and the Cannes Film Festival. The Promenade des Anglais in Nice and the old port of Antibes offer cultural depth alongside sun and glamour.', 'travel_medium_info' => 'Nice Cote d\'Azur Airport has direct international flights. Train from Paris takes 5.5 hrs (TGV). Best season: May-Sep for beaches.'],
        /* 17 */ ['title' => 'Mont Blanc & Chamonix',     'country' => 'France',    'genre' => 'mountain',   'cost_level' => 'high',   'base_cost' => 3000, 'short_history' => 'Western Europe\'s highest peak at 4,808 m towers over the resort town of Chamonix in the French Alps. The area offers world-class skiing (Vallee Blanche is legendary), mountaineering, summer hiking, and the breathtaking Aiguille du Midi cable car rising to 3,842 m with panoramic views across the Alps. The Mont Blanc Tunnel connects France to Italy.', 'travel_medium_info' => 'Fly to Geneva Airport (Switzerland) then 1.5-hr transfer to Chamonix by bus or train. Mont Blanc Express train runs from Geneva.'],
    ];
}

function featuredCtrl($conn)
{
    $key   = intval($_GET['dest'] ?? -1);
    $dests = getFeaturedDestinations();

    if ($key < 0 || $key >= count($dests)) {
        header('Location: index.php?page=browse');
        exit;
    }

    $dest = $dests[$key];
    $cost = ['base_cost' => $dest['base_cost'], 'currency' => 'USD'];
    $ck   = 'fcomments_' . $key;

    if (!isset($_SESSION[$ck])) $_SESSION[$ck] = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isVerifiedGeneralUser()) {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $content = trim($_POST['content'] ?? '');
            if ($content !== '' && strlen($content) <= 500) {
                $_SESSION[$ck][] = [
                    'id'         => uniqid('fc_', true),
                    'user_id'    => $_SESSION['user']['id'],
                    'name'       => $_SESSION['user']['name'],
                    'content'    => $content,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        } elseif ($action === 'delete') {
            $cid = $_POST['comment_id'] ?? '';
            $_SESSION[$ck] = array_values(array_filter($_SESSION[$ck], fn($c) => $c['id'] !== $cid));
        }

        header('Location: index.php?page=featured&dest=' . $key);
        exit;
    }

    $comments = array_reverse($_SESSION[$ck]);
    require 'views/featured_detail.php';
}
?>
