<?php
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/* ============== Login for local testing ============== */
function loginCtrl($conn) {
    $error  = '';
    $prefill = $_COOKIE['remember_user'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = trim($_POST['email'] ?? '');
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
            } else {
                $error = 'Invalid email or password.';
            }
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

function registerCtrl($conn) {
    $error = $success = '';
    $old   = ['name' => '', 'email' => '', 'role' => 'user'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $role     = $_POST['role'] ?? 'user';
        $old      = compact('name', 'email', 'role');

        if ($name === '' || $email === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
            if (registerUser($conn, $name, $email, $password, $role)) {
                $success = 'Account created! Please wait for admin approval.';
                $old     = ['name' => '', 'email' => '', 'role' => 'user'];
            } else {
                $error = 'Email already exists or registration failed.';
            }
        }
    }

    require 'views/register.php';
}

function homeCtrl($conn) {
    $user  = $_SESSION['user'] ?? null;
    $posts = [];

    if ($user && $user['verified']) {
        $posts = getApprovedPosts($conn, 6, 0);
    }

    require 'views/home.php';
}

/* ============== Wishlist ============== */
function wishlistCtrl($conn) {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }

    $user   = $_SESSION['user'];
    $userId = $user['id'];
    $items  = getUserWishlist($conn, $userId);

    require 'views/wishlist.php';
}

/* ============== AJAX: Wishlist Add ============== */
function ajaxWishlistAdd($conn) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? 0;
    $userId = $_SESSION['user']['id'];

    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid post ID']);
        exit;
    }

    if (addToWishlist($conn, $userId, $postId)) {
        echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Already in wishlist or failed']);
    }
    exit;
}

/* ============== AJAX: Wishlist Remove ============== */
function ajaxWishlistRemove($conn) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? 0;
    $userId = $_SESSION['user']['id'];

    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid post ID']);
        exit;
    }

    if (removeFromWishlist($conn, $userId, $postId)) {
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to remove']);
    }
    exit;
}

/* ============== AJAX: Wishlist Check ============== */
function ajaxWishlistCheck($conn) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? 0;
    $userId = $_SESSION['user']['id'];

    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid post ID']);
        exit;
    }

    echo json_encode(['in_wishlist' => isInWishlist($conn, $userId, $postId)]);
    exit;
}

function profileCtrl($conn) {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }

    $userId = $_SESSION['user']['id'];
    $user   = getUserById($conn, $userId);
    $error  = $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name            = trim($_POST['name'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $picture         = null;

        // File upload
        if (!empty($_FILES['picture']['name'])) {
            $file    = $_FILES['picture'];
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed)) {
                $error = 'Only JPG, PNG, GIF allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'File size must be under 2MB.';
            } else {
                $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
                $picture = 'uploads/' . uniqid() . '.' . $ext;
                if (!move_uploaded_file($file['tmp_name'], $picture)) {
                    $error   = 'File upload failed.';
                    $picture = null;
                }
            }
        }

        // Validate inputs
        if ($name === '')                              $error = 'Name is required.';
        if ($email === '')                             $error = 'Email is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Invalid email format.';

        // Password change
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                $error = 'New password must be 8+ characters.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Passwords do not match.';
            }
        }

        if ($error === '') {
            updateUserProfile($conn, $userId, $name, $email,
                $newPassword !== '' ? $newPassword : null,
                $picture);
            $success = 'Profile updated successfully!';
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;
            $user = getUserById($conn, $userId);
        }
    }

    require 'views/profile.php';
}

/* ============== Admin Dashboard ============== */
function adminCtrl($conn) {
    // Check admin role
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: index.php?page=home');
        exit;
    }

    $submodule = $_GET['module'] ?? 'dashboard';
    
    // Initialize variables
    $allUsers = array();
    $pendingRequests = array();
    $approvedPosts = array();
    $allComments = array();
    $adminStats = array();
    $error = '';
    $success = '';
    
    // Get admin statistics
    $adminStats = getAdminStats($conn);
    
    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($submodule === 'users') {
            if (isset($_POST['add_user'])) {
                $name = trim($_POST['user_name'] ?? '');
                $email = trim($_POST['user_email'] ?? '');
                $password = $_POST['user_password'] ?? '';
                $role = $_POST['user_role'] ?? 'user';
                
                if (empty($name) || empty($email) || empty($password)) {
                    $error = 'All fields are required.';
                } else {
                    if (addUserByAdmin($conn, $name, $email, $password, $role)) {
                        $success = 'User added successfully.';
                    } else {
                        $error = 'Email already exists or user add failed.';
                    }
                }
            } elseif (isset($_POST['verify_user'])) {
                $userId = $_POST['user_id'] ?? 0;
                if (toggleUserVerification($conn, $userId)) {
                    $success = 'User verification toggled.';
                } else {
                    $error = 'Failed to toggle verification.';
                }
            } elseif (isset($_POST['delete_user'])) {
                $userId = $_POST['user_id'] ?? 0;
                if ($userId != $_SESSION['user']['id']) {
                    if (deleteUserCascade($conn, $userId)) {
                        $success = 'User deleted successfully.';
                    } else {
                        $error = 'Failed to delete user.';
                    }
                } else {
                    $error = 'You cannot delete your own admin account.';
                }
            }
        } elseif ($submodule === 'posts') {
            if (isset($_POST['approve_post'])) {
                $requestId = $_POST['request_id'] ?? 0;
                if (approvePostRequest($conn, $requestId)) {
                    $success = 'Post request approved.';
                } else {
                    $error = 'Failed to approve post request.';
                }
            } elseif (isset($_POST['reject_post'])) {
                $requestId = $_POST['request_id'] ?? 0;
                if (rejectPostRequest($conn, $requestId)) {
                    $success = 'Post request rejected.';
                } else {
                    $error = 'Failed to reject post request.';
                }
            } elseif (isset($_POST['delete_post'])) {
                $postId = $_POST['post_id'] ?? 0;
                if (deletePostCascade($conn, $postId)) {
                    $success = 'Post deleted successfully.';
                } else {
                    $error = 'Failed to delete post.';
                }
            } elseif (isset($_POST['update_post'])) {
                $postId = $_POST['post_id'] ?? 0;
                $title = $_POST['post_title'] ?? '';
                $history = $_POST['post_history'] ?? '';
                $country = $_POST['post_country'] ?? '';
                $genre = $_POST['post_genre'] ?? '';
                $cost_level = $_POST['post_cost_level'] ?? '';
                $travel_info = $_POST['post_travel_info'] ?? '';
                
                if (updatePost($conn, $postId, $title, $history, $country, $genre, $cost_level, $travel_info)) {
                    $success = 'Post updated successfully.';
                } else {
                    $error = 'Failed to update post.';
                }
            }
        } elseif ($submodule === 'comments') {
            if (isset($_POST['delete_comment'])) {
                $commentId = $_POST['comment_id'] ?? 0;
                if (deleteComment($conn, $commentId)) {
                    $success = 'Comment deleted successfully.';
                } else {
                    $error = 'Failed to delete comment.';
                }
            }
        }
    }
    
    // Load appropriate data based on module
    if ($submodule === 'users') {
        $allUsers = getAllUsers($conn);
    } elseif ($submodule === 'posts') {
        $pendingRequests = getPendingPostRequests($conn);
        $approvedPosts = getApprovedPostsForModeration($conn);
    } elseif ($submodule === 'comments') {
        $allComments = getAllComments($conn);
    }
    
    require 'views/admin.php';
}

/* ============== Admin AJAX Handler ============== */
function adminAjax($conn) {
    header('Content-Type: application/json');
    
    // Check admin role
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    
    $action = $_GET['action'] ?? '';
    $response = ['status' => 'error', 'message' => 'Unknown action'];
    
    if ($action === 'toggle_verify') {
        $userId = $_GET['user_id'] ?? 0;
        if (toggleUserVerification($conn, $userId)) {
            $response = ['status' => 'success', 'message' => 'User verification toggled'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to toggle verification'];
        }
    } elseif ($action === 'delete_comment') {
        $commentId = $_GET['comment_id'] ?? 0;
        if (deleteComment($conn, $commentId)) {
            $response = ['status' => 'success', 'message' => 'Comment deleted'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to delete comment'];
        }
    } elseif ($action === 'approve_post') {
        $requestId = $_GET['request_id'] ?? 0;
        if (approvePostRequest($conn, $requestId)) {
            $response = ['status' => 'success', 'message' => 'Post approved'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to approve post'];
        }
    }
    
    echo json_encode($response);
    exit;
}
//Done by Ramim
//Ramim change-1
function scoutCtrl($conn) {
    
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }
    
    $user = $_SESSION['user'];
    $scoutId = $user['id'];
    
    // Only scouts and admins can access scout panel
    if ($user['role'] !== 'scout' && $user['role'] !== 'admin') {
        header('Location: index.php?page=home');
        exit;
    }
    
    // Initialize variables
    $errors = [];
    $success = '';
    $error = '';
    $old_input = [];
    
    // Handle post request submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post_request'])) {
        $title = trim($_POST['title'] ?? '');
        $short_history = trim($_POST['short_history'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $genre = $_POST['genre'] ?? '';
        $cost_level = $_POST['cost_level'] ?? '';
        $travel_medium_info = trim($_POST['travel_medium_info'] ?? '');
        
        // Store old input for repopulating form
        $old_input = [
            'title' => $title,
            'short_history' => $short_history,
            'country' => $country,
            'genre' => $genre,
            'cost_level' => $cost_level,
            'travel_medium_info' => $travel_medium_info
        ];
        
        // Individual field validation
        if (empty($title)) {
            $errors['title'] = "Title is required";
        }
        if (empty($short_history)) {
            $errors['short_history'] = "Short history is required";
        }
        if (empty($country)) {
            $errors['country'] = "Country is required";
        }
        if (empty($genre)) {
            $errors['genre'] = "Please select a genre";
        }
        if (empty($cost_level)) {
            $errors['cost_level'] = "Please select a cost level";
        }
        if (empty($travel_medium_info)) {
            $errors['travel_medium_info'] = "Travel medium info is required";
        }
        
        // If no errors, insert into database
        if (empty($errors)) {
            $post_data = json_encode([
                'title' => $title,
                'short_history' => $short_history,
                'country' => $country,
                'genre' => $genre,
                'cost_level' => $cost_level,
                'travel_medium_info' => $travel_medium_info
            ]);
            
            $stmt = mysqli_prepare($conn, "INSERT INTO post_requests (scout_id, post_data, status) VALUES (?, ?, 'pending')");
            mysqli_stmt_bind_param($stmt, 'is', $scoutId, $post_data);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Post request submitted successfully! Waiting for admin approval.";
                // Clear old input on success
                $old_input = [];
                // Redirect to prevent form resubmission
                header("Location: index.php?page=scout&success=1");
                exit;
            } else {
                $error = "Failed to submit request. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Please fill the required fields";
        }
    }
    
    // Check for success message from redirect
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        $success = "Post request submitted successfully! Waiting for admin approval.";
    }
    
    // Get scout's pending requests
    $stmt = mysqli_prepare($conn, "SELECT * FROM post_requests WHERE scout_id = ? ORDER BY requested_at DESC");
    mysqli_stmt_bind_param($stmt, 'i', $scoutId);
    mysqli_stmt_execute($stmt);
    $requests = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    // Get approved posts from posts table
    $stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE status = 'approved' ORDER BY created_at DESC");
    mysqli_stmt_execute($stmt);
    $posts = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    require 'views/scout.php';
}

//Ramim change-2
function scoutRequestsCtrl($conn) {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }
    
    $user = $_SESSION['user'];
    
    // Only scouts and admins can access
    if ($user['role'] !== 'scout' && $user['role'] !== 'admin') {
        header('Location: index.php?page=home');
        exit;
    }
    
    $scoutId = $user['id'];
    $message = '';
    $error = '';
    $editRequest = null;
    $edit_errors = [];
    
    // Check for success message from redirect
    if (isset($_GET['updated']) && $_GET['updated'] == 1) {
        $message = "Request updated successfully!";
    }
    
    if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
        $message = "Request deleted successfully!";
    }
    
    // Handle Edit - GET request to get data for editing
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $requestId = $_GET['edit'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM post_requests WHERE id = ? AND scout_id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, 'ii', $requestId, $scoutId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $editRequest = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$editRequest) {
            $error = "Request not found or cannot be edited.";
        }
    }
    
    // Handle Update (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request'])) {
        $requestId = $_POST['request_id'];
        $title = trim($_POST['title'] ?? '');
        $short_history = trim($_POST['short_history'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $genre = $_POST['genre'] ?? '';
        $cost_level = $_POST['cost_level'] ?? '';
        $travel_medium_info = trim($_POST['travel_medium_info'] ?? '');
        
        // PHP Validation
        if (empty($title)) $edit_errors['title'] = "Title is required";
        if (empty($short_history)) $edit_errors['short_history'] = "Short history is required";
        if (empty($country)) $edit_errors['country'] = "Country is required";
        if (empty($genre)) $edit_errors['genre'] = "Please select a genre";
        if (empty($cost_level)) $edit_errors['cost_level'] = "Please select a cost level";
        if (empty($travel_medium_info)) $edit_errors['travel_medium_info'] = "Travel medium info is required";
        
        if (empty($edit_errors)) {
            $post_data = json_encode([
                'title' => $title,
                'short_history' => $short_history,
                'country' => $country,
                'genre' => $genre,
                'cost_level' => $cost_level,
                'travel_medium_info' => $travel_medium_info
            ]);
            
            $stmt = mysqli_prepare($conn, "UPDATE post_requests SET post_data = ? WHERE id = ? AND scout_id = ? AND status = 'pending'");
            mysqli_stmt_bind_param($stmt, 'sii', $post_data, $requestId, $scoutId);
            
            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?page=scoutrequests&updated=1");
                exit;
            } else {
                $error = "Failed to update request.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Please fill the required fields";
            // Fetch the request again to show the edit form with errors
            $stmt = mysqli_prepare($conn, "SELECT * FROM post_requests WHERE id = ? AND scout_id = ? AND status = 'pending'");
            mysqli_stmt_bind_param($stmt, 'ii', $requestId, $scoutId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $editRequest = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
        }
    }
    
    // Handle Delete (POST for delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
        $requestId = $_POST['request_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM post_requests WHERE id = ? AND scout_id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, 'ii', $requestId, $scoutId);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?page=scoutrequests&deleted=1");
            exit;
        } else {
            $error = "Failed to delete request.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Get all requests for this scout (only pending new post requests, not change requests)
    $stmt = mysqli_prepare($conn, "SELECT * FROM post_requests WHERE scout_id = ? AND status = 'pending' AND original_post_id IS NULL ORDER BY requested_at DESC");
    mysqli_stmt_bind_param($stmt, 'i', $scoutId);
    mysqli_stmt_execute($stmt);
    $requests = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    require 'views/scoutrequests.php';
}

//Ramim change-3
function approvedPostsCtrl($conn) {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }
    
    $user = $_SESSION['user'];
    $scoutId = $user['id'];
    
    // Only scouts and admins can access
    if ($user['role'] !== 'scout' && $user['role'] !== 'admin') {
        header('Location: index.php?page=home');
        exit;
    }
    
    // Handle change request submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_change_request'])) {
        $original_post_id = $_POST['original_post_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $short_history = trim($_POST['short_history'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $genre = $_POST['genre'] ?? '';
        $cost_level = $_POST['cost_level'] ?? '';
        $travel_medium_info = trim($_POST['travel_medium_info'] ?? '');
        
        $errors = [];
        
        if (empty($title)) $errors[] = "Title is required";
        if (empty($short_history)) $errors[] = "Short history is required";
        if (empty($country)) $errors[] = "Country is required";
        if (empty($genre)) $errors[] = "Genre is required";
        if (empty($cost_level)) $errors[] = "Cost level is required";
        if (empty($travel_medium_info)) $errors[] = "Travel medium info is required";
        
        if (empty($errors)) {
            $post_data = json_encode([
                'title' => $title,
                'short_history' => $short_history,
                'country' => $country,
                'genre' => $genre,
                'cost_level' => $cost_level,
                'travel_medium_info' => $travel_medium_info
            ]);
            
            $stmt = mysqli_prepare($conn, "INSERT INTO post_requests (scout_id, original_post_id, post_data, status) VALUES (?, ?, ?, 'pending')");
            mysqli_stmt_bind_param($stmt, 'iis', $scoutId, $original_post_id, $post_data);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Change request submitted successfully! Waiting for admin approval.";
            } else {
                $error = "Failed to submit change request.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = implode(", ", $errors);
        }
    }
    
    // Get all approved posts
    $stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE status = 'approved' ORDER BY created_at DESC");
    mysqli_stmt_execute($stmt);
    $posts = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    require 'views/scoutapprovedposts.php';
}

//Ramim change-4
function ajaxChangeRequest($conn) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user'])) {
        echo json_encode(['error' => 'Not logged in', 'success' => false]);
        exit;
    }
    
    $user = $_SESSION['user'];
    $scoutId = $user['id'];
    
    // Only scouts and admins can submit change requests
    if ($user['role'] !== 'scout' && $user['role'] !== 'admin') {
        echo json_encode(['error' => 'Unauthorized', 'success' => false]);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $original_post_id = $data['original_post_id'] ?? 0;
    $title = trim($data['title'] ?? '');
    $short_history = trim($data['short_history'] ?? '');
    $country = trim($data['country'] ?? '');
    $genre = $data['genre'] ?? '';
    $cost_level = $data['cost_level'] ?? '';
    $travel_medium_info = trim($data['travel_medium_info'] ?? '');
    
    // Validate
    if (empty($title) || empty($short_history) || empty($country) || empty($genre) || empty($cost_level) || empty($travel_medium_info)) {
        echo json_encode(['error' => 'All fields are required', 'success' => false]);
        exit;
    }
    
    $post_data = json_encode([
        'title' => $title,
        'short_history' => $short_history,
        'country' => $country,
        'genre' => $genre,
        'cost_level' => $cost_level,
        'travel_medium_info' => $travel_medium_info
    ]);
    
    $stmt = mysqli_prepare($conn, "INSERT INTO post_requests (scout_id, original_post_id, post_data, status) VALUES (?, ?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, 'iis', $scoutId, $original_post_id, $post_data);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => mysqli_error($conn), 'success' => false]);
    }
    mysqli_stmt_close($stmt);
}

//Ramim till here