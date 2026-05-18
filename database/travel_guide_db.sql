-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 05:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_guide_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cost_estimates`
--

CREATE TABLE `cost_estimates` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `base_cost` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `scout_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `short_history` text NOT NULL,
  `country` varchar(100) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `cost_level` enum('low','medium','high') NOT NULL,
  `travel_medium_info` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `scout_id`, `title`, `short_history`, `country`, `genre`, `cost_level`, `travel_medium_info`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 'Taj Mahal, Agra', 'One of the Seven Wonders of the World, this ivory-white marble mausoleum was built by Mughal emperor Shah Jahan in memory of his beloved wife Mumtaz Mahal. Set on the banks of the Yamuna River in Agra, the complex spans 17 hectares and took 22 years to complete. The Taj Mahal is renowned for its perfect symmetry, intricate inlay work, and the way it changes colour with the light throughout the day.', 'India', 'historical', 'medium', 'Best reached by train from Delhi (2-3 hrs). Nearest airport is Agra Airport. Ideal months: Oct-Mar.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(3, 1, 'Goa Beaches', 'Famous for its stunning coastline, vibrant nightlife, and Portuguese-influenced architecture, Goa is India\'s most beloved beach destination with over 100 km of pristine shores. From the lively Baga and Calangute beaches to the tranquil Palolem and Agonda in the south, each beach has its own personality. Explore spice plantations, colonial churches, and a unique blend of Indian and European cultures.', 'India', 'beach', 'medium', 'Goa International Airport connects to major Indian cities. Trains from Mumbai take 8-12 hrs. Best season: Nov-Feb.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(4, 1, 'Kerala Backwaters', 'A network of interconnected canals, rivers, lakes, and inlets formed by more than 900 km of waterways stretching across Kerala. Overnight houseboat cruises through lush backwaters, paddy fields, and coconut groves offer a uniquely serene experience. Alappuzha (Alleppey) is the main hub; the famous Nehru Trophy Snake Boat Race takes place here every August.', 'India', 'beach', 'low', 'Fly into Cochin or Trivandrum airport. Houseboat rentals depart from Alleppey (Alappuzha). Best visited Oct-Feb.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(5, 1, 'Jaipur — The Pink City', 'The capital of Rajasthan, known for its stunning palaces, forts, and vibrant bazaars. The Amber Fort, Hawa Mahal (Palace of Winds), City Palace, and Jantar Mantar observatory make it a treasure trove of Rajput architecture. The entire old city is painted terracotta pink — a tradition dating back to 1876 when the city was painted for Prince Albert\'s visit.', 'India', 'historical', 'medium', 'Jaipur International Airport is well connected. Trains from Delhi take 4-5 hrs. Part of the Golden Triangle tourist circuit.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(6, 1, 'Manali, Himachal Pradesh', 'A high-altitude Himalayan resort town at 2,050 m, known for adventure sports including skiing, paragliding, river rafting, and trekking. Surrounded by snow-capped peaks, apple orchards, and dense cedar forests, Manali is also home to the ancient Hadimba Devi Temple. The Rohtang Pass (3,978 m) offers dramatic views and snow even in summer.', 'India', 'mountain', 'low', 'Nearest airport is Bhuntar (Kullu). Volvo bus from Delhi takes 14-16 hrs. Best season: Mar-Jun for treks; Dec-Jan for snow.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(7, 1, 'Sigiriya Rock Fortress', 'An ancient rock fortress and palace ruin rising nearly 200 m above the surrounding plains. Built by King Kashyapa I in the 5th century AD, it features remarkable frescoes of celestial maidens, a mirror wall with ancient graffiti, landscaped water gardens, and a lion-paw entrance staircase. Now a UNESCO World Heritage Site and one of Sri Lanka\'s most visited monuments.', 'Sri Lanka', 'historical', 'medium', 'Fly into Colombo Bandaranaike Airport then drive 4-5 hrs. Nearest town is Dambulla. Best visited during dry season (May-Sep).', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(8, 1, 'Mirissa Beach', 'A picturesque crescent-shaped bay on the southern coast of Sri Lanka, famous for its relaxed atmosphere and turquoise waters. Mirissa is one of the best places in the world for blue whale watching (Dec-Apr). Surfers, backpackers, and couples alike love the palm-fringed beach, fresh seafood, and stunning sunsets. Coconut Tree Hill viewpoint offers a memorable panoramic photograph.', 'Sri Lanka', 'beach', 'low', 'Drive 3 hrs south from Colombo. Nearest town is Weligama. Blue whale watching tours run Dec-Apr. Best surf season Nov-Apr.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(9, 1, 'Ella Hill Country', 'A small town nestled in the green hills of Sri Lanka\'s highland tea country at around 1,000 m elevation. Ella is famous for the iconic Nine Arches Bridge, the hike up Little Adam\'s Peak, Ella Rock, and sweeping views over Ravana Falls. The area is blanketed in lush tea plantations; visitors can tour a tea factory and sample fresh Ceylon tea.', 'Sri Lanka', 'mountain', 'low', 'Take the scenic train from Kandy (6-7 hrs) — one of the world\'s most beautiful rail journeys. Best visited Mar-Aug.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(10, 1, 'Colombo', 'Sri Lanka\'s bustling commercial capital blends colonial heritage with a rapidly modernising skyline. The Pettah Bazaar is a sensory feast of spices, fabrics, and street food. Key highlights include the National Museum, Gangaramaya Temple, the scenic Galle Face Green promenade, and the vibrant Colombo 07 neighbourhood with galleries, boutiques, and cafes.', 'Sri Lanka', 'city', 'medium', 'Bandaranaike International Airport is 30 km north of the city. Colombo is the main entry point for Sri Lanka. Well-connected by bus and train.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(11, 1, 'Temple of the Tooth, Kandy', 'Sri Dalada Maligawa, the Temple of the Sacred Tooth Relic, is a royal palace complex in the hill city of Kandy that houses Buddhism\'s most venerated relic — a tooth of the historical Buddha. The temple sits beside the picturesque Kandy Lake, within the last royal capital of Sri Lanka. The annual Esala Perahera festival features elaborately decorated elephants, fire dancers, and drummers.', 'Sri Lanka', 'historical', 'medium', 'Kandy is 115 km from Colombo (3-4 hrs by train or bus). Best visited during the Esala Perahera festival (Jul-Aug).', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(12, 1, 'Everest Base Camp', 'The ultimate trekking challenge, Everest Base Camp (5,364 m) draws thousands of adventurers each year to the foot of the world\'s highest peak. The classic Khumbu route passes through Namche Bazaar, Tengboche Monastery, Dingboche, and Gorak Shep before reaching base camp. Along the way trekkers experience dramatic glacier views, Sherpa culture, yak caravans, and breathtaking high-altitude scenery.', 'Nepal', 'mountain', 'high', 'Fly Kathmandu to Lukla (35 min) then 12-14 day trek to base camp at 5,364 m. Best seasons: Mar-May and Sep-Nov. Permits required.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(13, 1, 'Kathmandu Valley', 'A UNESCO World Heritage valley containing seven monument zones, including Pashupatinath Temple (one of Hinduism\'s holiest sites), Boudhanath Stupa (the world\'s largest), Swayambhunath (Monkey Temple), and the medieval royal squares of Kathmandu, Patan, and Bhaktapur. The valley is the cultural heart of Nepal, blending Hindu and Buddhist traditions with remarkable art and architecture.', 'Nepal', 'historical', 'low', 'Tribhuvan International Airport is in Kathmandu. Good road and air connections across Nepal. Best visited Oct-Nov and Mar-Apr.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(14, 1, 'Pokhara', 'Nepal\'s adventure capital sits at 820 m on the shores of the serene Phewa Lake, with the dramatic Annapurna massif and Machhapuchhre (Fish Tail) as a stunning backdrop. Pokhara is famous for paragliding, ultra-light flights, zip-lining, kayaking, and being the starting point of the Annapurna Circuit and Sanctuary treks. The Lakeside area has a relaxed, cosmopolitan vibe with mountain views.', 'Nepal', 'mountain', 'low', '25-min flight or 7-hr bus from Kathmandu. Pokhara is the gateway to Annapurna treks. Best seasons: Oct-Nov and Mar-Apr.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(15, 1, 'Chitwan National Park', 'Nepal\'s first national park and a UNESCO World Heritage Site, covering 952 sq km of subtropical lowland forest and grassland. Home to the endangered one-horned rhinoceros, Bengal tiger, gharial crocodile, and over 600 bird species. Activities include jeep safaris, canoe rides, jungle walks, elephant bathing, and visits to the Tharu cultural village. One of Asia\'s best wildlife destinations.', 'Nepal', 'mountain', 'medium', 'Drive 5-6 hrs from Kathmandu or take a short flight to Bharatpur. Safaris depart from Sauraha village. Best season: Oct-Mar.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(16, 1, 'Annapurna Circuit', 'Considered one of the world\'s greatest trekking routes, the Annapurna Circuit circumnavigates the Annapurna massif through diverse landscapes — subtropical forests, rice terraces, alpine meadows, the arid trans-Himalayan zone of Mustang, and glaciated high peaks. Highlights include the Thorong La pass (5,416 m), Muktinath Temple, and the ancient town of Manang.', 'Nepal', 'mountain', 'medium', 'Start from Besisahar (5 hrs from Kathmandu by bus). Full circuit takes 12-21 days. Thorong La pass at 5,416 m. Permits required.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(17, 1, 'Paris — City of Light', 'The iconic French capital is home to the Eiffel Tower, the Louvre Museum (world\'s largest art museum), Notre-Dame Cathedral, the Musee d\'Orsay, and the Champs-Elysees. Stroll along the Seine, explore the Marais district, and experience world-class cuisine and fashion. Paris hosts 30 million visitors per year and is consistently voted the world\'s most visited city.', 'France', 'city', 'high', 'Charles de Gaulle Airport is the main international hub. Excellent metro and RER network within the city. Eurostar connects London in 2.5 hrs. Best visited spring (Apr-Jun) or autumn (Sep-Oct).', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(18, 1, 'French Riviera (Cote d\'Azur)', 'The glamorous Mediterranean coastline stretching from Menton near the Italian border to Toulon, encompassing Nice, Cannes, Antibes, Saint-Tropez, and Monaco. Famous for its azure waters, luxury yachts, pebble beaches, and the Cannes Film Festival. The Promenade des Anglais in Nice and the old port of Antibes offer cultural depth alongside sun and glamour.', 'France', 'beach', 'high', 'Nice Cote d\'Azur Airport has direct international flights. Train from Paris takes 5.5 hrs (TGV). Best season: May-Sep for beaches.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36'),
(19, 1, 'Mont Blanc & Chamonix', 'Western Europe\'s highest peak at 4,808 m towers over the resort town of Chamonix in the French Alps. The area offers world-class skiing (Vallee Blanche is legendary), mountaineering, summer hiking, and the breathtaking Aiguille du Midi cable car rising to 3,842 m with panoramic views across the Alps. The Mont Blanc Tunnel connects France to Italy.', 'France', 'mountain', 'high', 'Fly to Geneva Airport (Switzerland) then 1.5-hr transfer to Chamonix by bus or train. Mont Blanc Express train runs from Geneva.', 'approved', '2026-05-18 15:09:36', '2026-05-18 15:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `post_requests`
--

CREATE TABLE `post_requests` (
  `id` int(11) NOT NULL,
  `scout_id` int(11) NOT NULL,
  `original_post_id` int(11) DEFAULT NULL,
  `post_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`post_data`)),
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_requests`
--

INSERT INTO `post_requests` (`id`, `scout_id`, `original_post_id`, `post_data`, `requested_at`, `status`) VALUES
(1, 10, 1, '{\"title\":\"\\u09b8\\u09be\\u09b0\\u09bf\\u0998\\u09be\\u099f\",\"short_history\":\"\\u09b6\\u09b9\\u09b0\\u09c7\\u09b0 \\u09af\\u09be\\u09a8\\u09cd\\u09a4\\u09cd\\u09b0\\u09bf\\u0995 \\u0995\\u09cb\\u09b2\\u09be\\u09b9\\u09b2 \\u09a5\\u09c7\\u0995\\u09c7 \\u09a6\\u09c2\\u09b0\\u09c7, \\u0985\\u09aa\\u09b0\\u09c2\\u09aa \\u09aa\\u09cd\\u09b0\\u0995\\u09c3\\u09a4\\u09bf\\u09b0 \\u09ae\\u09be\\u099d\\u09c7 \\u098f\\u0995\\u099f\\u09bf \\u09b6\\u09be\\u09a8\\u09cd\\u09a4 \\u09ac\\u09bf\\u0995\\u09c7\\u09b2 \\u0995\\u09be\\u099f\\u09be\\u09a4\\u09c7 \\u099a\\u09be\\u0987\\u09b2\\u09c7 \\u09ac\\u09a8\\u09cd\\u09a7\\u09c1\\u09ac\\u09be\\u09a8\\u09cd\\u09a7\\u09ac \\u09ac\\u09be \\u09aa\\u09b0\\u09bf\\u09ac\\u09be\\u09b0 \\u09a8\\u09bf\\u09df\\u09c7 \\u09ac\\u09c7\\u09dc\\u09bf\\u09df\\u09c7 \\u0986\\u09b8\\u09a4\\u09c7 \\u09aa\\u09be\\u09b0\\u09c7\\u09a8 \\u0995\\u09c7\\u09b0\\u09be\\u09a8\\u09c0\\u0997\\u099e\\u09cd\\u099c\\u09c7\\u09b0 \\u09b8\\u09be\\u09b0\\u09bf\\u0998\\u09be\\u099f (Sarighat) \\u09a5\\u09c7\\u0995\\u09c7\\u0964 \\u09a2\\u09be\\u0995\\u09be\\u09b0 \\u09a6\\u0995\\u09cd\\u09b7\\u09bf\\u09a3 \\u09aa\\u09cd\\u09b0\\u09be\\u09a8\\u09cd\\u09a4\\u09c7 \\u09ac\\u09c1\\u09dc\\u09bf\\u0997\\u0999\\u09cd\\u0997\\u09be \\u0993 \\u09a7\\u09b2\\u09c7\\u09b6\\u09cd\\u09ac\\u09b0\\u09c0 \\u09a8\\u09a6\\u09c0\\u09b0 \\u0985\\u09ac\\u09ac\\u09be\\u09b9\\u09bf\\u0995\\u09be\\u09df, \\u09b9\\u09be\\u09b8\\u09a8\\u09be\\u09ac\\u09be\\u09a6\\u09c7\\u09b0 \\u09ac\\u09b8\\u09c1\\u09a8\\u09cd\\u09a7\\u09b0\\u09be \\u09b0\\u09bf\\u09ad\\u09be\\u09b0\\u09ad\\u09bf\\u0989 \\u09aa\\u09cd\\u09b0\\u099c\\u09c7\\u0995\\u09cd\\u099f\\u09c7\\u09b0 \\u09a0\\u09bf\\u0995 \\u09aa\\u09c7\\u099b\\u09a8\\u09c7 \\u098f\\u0987 \\u09a6\\u09b0\\u09cd\\u09b6\\u09a8\\u09c0\\u09df \\u09b8\\u09cd\\u09a5\\u09be\\u09a8\\u099f\\u09bf\\u09b0 \\u0985\\u09ac\\u09b8\\u09cd\\u09a5\\u09be\\u09a8\\u0964 \\u0986\\u0987\\u09a8\\u09cd\\u09a4\\u09be \\u0993 \\u0986\\u09dc\\u09be\\u0995\\u09c1\\u09b2 \\u098f\\u0987 \\u09a6\\u09c1\\u0987 \\u0997\\u09cd\\u09b0\\u09be\\u09ae\\u09c7\\u09b0 \\u09ae\\u09a7\\u09cd\\u09af \\u09a6\\u09bf\\u09df\\u09c7 \\u09ac\\u09df\\u09c7 \\u09af\\u09be\\u0993\\u09df\\u09be \\u098f\\u0995\\u099f\\u09bf \\u0996\\u09be\\u09b2\\u09c7\\u09b0 \\u09aa\\u09be\\u09dc \\u09a7\\u09b0\\u09c7 \\u09aa\\u09cd\\u09b0\\u09be\\u09df \\u098f\\u0995 \\u0995\\u09bf\\u09b2\\u09cb\\u09ae\\u09bf\\u099f\\u09be\\u09b0 \\u098f\\u09b2\\u09be\\u0995\\u09be \\u099c\\u09c1\\u09dc\\u09c7 \\u09b0\\u09cb\\u09aa\\u09a3 \\u0995\\u09b0\\u09be \\u09b9\\u09df\\u09c7\\u099b\\u09c7 \\u09b8\\u09be\\u09b0\\u09bf \\u09b8\\u09be\\u09b0\\u09bf \\u0995\\u09b0\\u0987 \\u0997\\u09be\\u099b\\u0964 \\u099b\\u09be\\u09df\\u09be \\u09b8\\u09c1\\u09a8\\u09bf\\u09ac\\u09bf\\u09dc \\u098f\\u0987 \\u0995\\u09b0\\u0987 \\u0997\\u09be\\u099b\\u09c7\\u09b0 \\u099a\\u09ae\\u09ce\\u0995\\u09be\\u09b0 \\u09b8\\u09be\\u09b0\\u09bf\\u09b0 \\u0995\\u09be\\u09b0\\u09a3\\u09c7\\u0987 \\u09b8\\u09cd\\u09a5\\u09be\\u09a8\\u099f\\u09bf \\u09aa\\u09cd\\u09b0\\u0995\\u09c3\\u09a4\\u09bf\\u09aa\\u09cd\\u09b0\\u09c7\\u09ae\\u09c0\\u09a6\\u09c7\\u09b0 \\u0995\\u09be\\u099b\\u09c7 \\u2018\\u09b8\\u09be\\u09b0\\u09bf\\u0998\\u09be\\u099f\\u2019 \\u09a8\\u09be\\u09ae\\u09c7 \\u09aa\\u09b0\\u09bf\\u099a\\u09bf\\u09a4\\u09bf \\u09b2\\u09be\\u09ad \\u0995\\u09b0\\u09c7\\u099b\\u09c7\\u0964\",\"country\":\"Bangladesh\",\"genre\":\"beach\",\"cost_level\":\"medium\",\"travel_medium_info\":\"\\u09b8\\u09be\\u09b0\\u09bf\\u0998\\u09be\\u099f \\u09af\\u09c7\\u09a4\\u09c7 \\u09b9\\u09b2\\u09c7 \\u09a2\\u09be\\u0995\\u09be\\u09b0 \\u09af\\u09c7\\u0995\\u09cb\\u09a8 \\u09b8\\u09cd\\u09a5\\u09be\\u09a8 \\u09b9\\u09a4\\u09c7 \\u09aa\\u09cd\\u09b0\\u09a5\\u09ae\\u09c7 \\u09af\\u09be\\u09a4\\u09cd\\u09b0\\u09be\\u09ac\\u09be\\u09dc\\u09c0 \\u099a\\u09b2\\u09c7 \\u0986\\u09b8\\u09c1\\u09a8\\u0964 \\u09af\\u09be\\u09a4\\u09cd\\u09b0\\u09be\\u09ac\\u09be\\u09dc\\u09c0 \\u09a5\\u09c7\\u0995\\u09c7 \\u099c\\u09c1\\u09b0\\u09be\\u0987\\u09a8 \\u09b0\\u09c7\\u09b2\\u0997\\u09c7\\u099f \\u098f\\u09b8\\u09c7 \\u09ac\\u09be\\u09b8 \\u09ac\\u09be \\u09b2\\u09c7\\u0997\\u09c1\\u09a8\\u09be\\u09df \\u099a\\u09dc\\u09c7 \\u099a\\u09b2\\u09c7 \\u09af\\u09be\\u09a8 \\u09aa\\u09cb\\u09b8\\u09cd\\u09a4\\u0997\\u09cb\\u09b2\\u09be \\u09ac\\u09cd\\u09b0\\u09bf\\u099c\\u09c7\\u09b0 \\u0985\\u09a8\\u09cd\\u09af \\u09aa\\u09be\\u09dc\\u09c7 (\\u09b9\\u09be\\u09b8\\u09a8\\u09be\\u09ac\\u09be\\u09a6 \\u09aa\\u09cd\\u09b0\\u09be\\u09a8\\u09cd\\u09a4)\\u0964 \\u09aa\\u09cb\\u09b8\\u09cd\\u09a4\\u0997\\u09cb\\u09b2\\u09be \\u09ac\\u09cd\\u09b0\\u09bf\\u099c\\u09c7\\u09b0 \\u0997\\u09cb\\u09dc\\u09be \\u09a5\\u09c7\\u0995\\u09c7\\u0987 \\u09b8\\u09be\\u09b0\\u09bf\\u0998\\u09be\\u099f \\u09af\\u09be\\u0993\\u09df\\u09be\\u09b0 \\u09b2\\u09cb\\u0995\\u09be\\u09b2 \\u0985\\u099f\\u09cb \\u098f\\u09ac\\u0982 \\u09b8\\u09bf\\u098f\\u09a8\\u099c\\u09bf \\u09aa\\u09be\\u0993\\u09df\\u09be \\u09af\\u09be\\u09df, \\u09af\\u09be\\u09a4\\u09c7 \\u099a\\u09dc\\u09c7 \\u09b8\\u09b0\\u09be\\u09b8\\u09b0\\u09bf \\u09aa\\u09cc\\u0981\\u099b\\u09c7 \\u09af\\u09c7\\u09a4\\u09c7 \\u09aa\\u09be\\u09b0\\u09ac\\u09c7\\u09a8 \\u0986\\u09aa\\u09a8\\u09be\\u09b0 \\u0997\\u09a8\\u09cd\\u09a4\\u09ac\\u09cd\\u09af\\u09c7\\u0964\"}', '2026-05-18 14:56:22', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','scout','user') NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `profile_picture` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_verified`, `profile_picture`, `remember_token`, `created_at`) VALUES
(7, 'Mahin Emon ss', 'mahinahmad911@gmail.com', '$2y$10$Dma/it80K7imyQIGBZZvN.wIxrqD8pX2OYQOGdSPQDO7U7buT47FW', 'user', 1, NULL, NULL, '2026-05-17 13:09:03'),
(9, 'User1', 'User1@email.com', '$2y$10$9txg1mNeJKnk6Ozn5p7iK.tI50FTgFMnh1ujt3m1bLj0VXfaoALGy', 'user', 1, NULL, NULL, '2026-05-17 14:23:54'),
(10, 'User2', 'User2@email.com', '$2y$10$hOSIlCmJFDW1GEVKhWZC7.ncSc2I298pF43YMHAjmDqyNcUpzaPiO', 'scout', 1, NULL, NULL, '2026-05-17 14:25:12'),
(11, 'Administrator', 'admin@example.com', '$2y$10$AfTThBPchBrhpOniVvt51ubaIs20bHF.me84UNTSrfDHcacx0XBKu', 'admin', 1, NULL, NULL, '2026-05-18 13:05:40'),
(12, 'Mahin Emon', 'email@ex.com', '$2y$10$.khQrTaRDbwIlXZR58mmOOqbLefyUfuQEQ5NqMCws1vJr3nzSNQdS', 'user', 0, NULL, NULL, '2026-05-18 13:17:10'),
(13, 'user41', 'user41@gmail.com', '$2y$10$dQHQhwu8TsFHJJlwRrOlE.4GoNYueUczkUW1mIsaGXEsjLJ0no1Y.', 'scout', 0, NULL, NULL, '2026-05-18 13:41:46'),
(14, 'user42', 'user42@gmail.com', '$2y$10$WI0p.vEnKOC5KbPvRb4UB.3AYurBP41X9ikGj287ucvRsKh9Y2ou6', 'user', 0, NULL, NULL, '2026-05-18 13:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `post_id`, `added_at`) VALUES
(8, 7, 1, '2026-05-17 13:33:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cost_estimates`
--
ALTER TABLE `cost_estimates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_requests`
--
ALTER TABLE `post_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`post_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_estimates`
--
ALTER TABLE `cost_estimates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `post_requests`
--
ALTER TABLE `post_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
