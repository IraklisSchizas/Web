-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1
-- Χρόνος δημιουργίας: 07 Ιουν 2024 στις 16:27:47
-- Έκδοση διακομιστή: 10.4.32-MariaDB
-- Έκδοση PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `user_db`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `announcements`
--

CREATE TABLE `announcements` (
  `id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `date` datetime NOT NULL,
  `item_ids` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `details`, `date`, `item_ids`) VALUES
(4, 'pros oloys', 'Plolu shmantiko mhnyma paidia oti kalytero', '2024-05-21 16:22:54', '16,17'),
(6, 'abc', 'defg', '2024-05-25 17:46:04', '17'),
(10, 'dd', 'ddsds', '2024-05-25 18:01:56', '16,17,18,19,20,24,56,71,84,85,91,101,108,116'),
(11, 'vdffd', 'dfdfdf', '2024-05-25 18:06:07', '18');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `cargo`
--

CREATE TABLE `cargo` (
  `id` int(255) NOT NULL,
  `rescuer_id` int(255) NOT NULL,
  `item_ids` varchar(255) NOT NULL,
  `quantity` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `cargo`
--

INSERT INTO `cargo` (`id`, `rescuer_id`, `item_ids`, `quantity`) VALUES
(5, 2, '17', 20),
(7, 2, '16', 2);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `categories`
--

CREATE TABLE `categories` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(5, 'Food'),
(6, 'Beverages'),
(7, 'Clothing'),
(8, 'Hacker of class'),
(9, '2d hacker'),
(10, ''),
(11, 'Test'),
(13, '-----'),
(14, 'Flood'),
(15, 'new cat'),
(16, 'Medical Supplies'),
(19, 'Shoes'),
(21, 'Personal Hygiene '),
(22, 'Cleaning Supplies'),
(23, 'Tools'),
(24, 'Kitchen Supplies'),
(25, 'Baby Essentials'),
(26, 'Insect Repellents'),
(27, 'Electronic Devices'),
(28, 'Cold weather'),
(29, 'Animal Food'),
(30, 'Financial support'),
(33, 'Cleaning Supplies.'),
(34, 'Hot Weather'),
(35, 'First Aid '),
(39, 'Test_0'),
(40, 'test1'),
(41, 'pet supplies'),
(42, 'Μedicines'),
(43, 'Energy Drinks'),
(44, 'Disability and Assistance Items'),
(45, 'Communication items'),
(46, 'communications'),
(47, 'Humanitarian Shelters'),
(48, 'Water Purification'),
(49, 'Animal Care'),
(50, 'Earthquake Safety'),
(51, 'Sleep Essentilals'),
(52, 'Navigation Tools'),
(53, 'Clothing and cover'),
(54, 'Tools and Equipment'),
(56, 'Special items'),
(57, 'Household Items');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `items`
--

CREATE TABLE `items` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` int(255) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `quantity` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `items`
--

INSERT INTO `items` (`id`, `name`, `category`, `details`, `quantity`) VALUES
(16, 'Water', 6, '[{\"detail_name\":\"Volume\",\"detail_value\":\"1.5l\"},{\"detail_name\":\"Pack size\",\"detail_value\":\"6\"}]', 13),
(17, 'Orange', 6, '[{\"detail_name\":\"Volume\",\"detail_value\":\"250ml\"},{\"detail_name\":\"Pack size\",\"detail_value\":\"12\"}]', 74),
(18, 'Sardines', 5, '[{\"detail_name\":\"brand\",\"detail_value\":\"Trata\"},{\"detail_name\":\"weight\",\"detail_value\":\"200g\"}]', 0),
(19, 'Canned corn', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"500g\"}]', 0),
(20, 'Bread', 5, '[{\"detail_name\":\"Weight\",\"detail_value\":\"1kg\"},{\"detail_name\":\"Type\",\"detail_value\":\"white\"}]', 0),
(21, 'Chocolate', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"100g\"},{\"detail_name\":\"type\",\"detail_value\":\"milk chocolate\"},{\"detail_name\":\"brand\",\"detail_value\":\"ION\"}]', 0),
(22, 'Men Sneakers', 7, '[{\"detail_name\":\"size\",\"detail_value\":\"44\"}]', 0),
(23, 'Test Product', 9, '[{\"detail_name\":\"weight\",\"detail_value\":\"500g\"},{\"detail_name\":\"pack size\",\"detail_value\":\"12\"},{\"detail_name\":\"expiry date\",\"detail_value\":\"13\\/12\\/1978\"}]', 0),
(24, 'Test Val', 14, '[{\"detail_name\":\"Details\",\"detail_value\":\"600ml\"}]', 0),
(25, 'Spaghetti', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"500\"}]', 0),
(26, 'Croissant', 5, '[{\"detail_name\":\"calories\",\"detail_value\":\"200\"}]', 0),
(28, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(29, 'Biscuits', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(30, 'Bandages', 16, '[{\"detail_name\":\"\",\"detail_value\":\"25 pcs\"}]', 0),
(31, 'Disposable gloves', 16, '[{\"detail_name\":\"\",\"detail_value\":\"100 pcs\"}]', 0),
(32, 'Gauze', 16, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(33, 'Antiseptic', 16, '[{\"detail_name\":\"\",\"detail_value\":\"250ml\"}]', 0),
(34, 'First Aid Kit', 16, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(35, 'Painkillers', 16, '[{\"detail_name\":\"volume\",\"detail_value\":\"200mg\"}]', 0),
(36, 'Blanket', 7, '[{\"detail_name\":\"size\",\"detail_value\":\"50x60\"}]', 0),
(37, 'Fakes', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(38, 'Menstrual Pads', 21, '[{\"detail_name\":\"stock\",\"detail_value\":\"500\"},{\"detail_name\":\"size\",\"detail_value\":\"3\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(39, 'Tampon', 21, '[{\"detail_name\":\"stock\",\"detail_value\":\"500\"},{\"detail_name\":\"size\",\"detail_value\":\"regular\"}]', 0),
(40, 'Toilet Paper', 21, '[{\"detail_name\":\"stock\",\"detail_value\":\"300\"},{\"detail_name\":\"ply\",\"detail_value\":\"3\"}]', 0),
(41, 'Baby wipes', 21, '[{\"detail_name\":\"volume\",\"detail_value\":\"500gr\"},{\"detail_name\":\"stock \",\"detail_value\":\"500\"},{\"detail_name\":\"scent\",\"detail_value\":\"aloe\"}]', 0),
(42, 'Toothbrush', 21, '[{\"detail_name\":\"stock\",\"detail_value\":\"500\"}]', 0),
(43, 'Toothpaste', 21, '[{\"detail_name\":\"stock\",\"detail_value\":\"250\"}]', 0),
(44, 'Vitamin C', 16, '[{\"detail_name\":\"stock\",\"detail_value\":\"200\"}]', 0),
(45, 'Multivitamines', 16, '[{\"detail_name\":\"stock\",\"detail_value\":\"200\"}]', 0),
(46, 'Paracetamol', 16, '[{\"detail_name\":\"stock\",\"detail_value\":\"2000\"},{\"detail_name\":\"dosage\",\"detail_value\":\"500mg\"}]', 0),
(47, 'Ibuprofen', 16, '[{\"detail_name\":\"stock \",\"detail_value\":\"10\"},{\"detail_name\":\"dosage\",\"detail_value\":\"200mg\"}]', 0),
(48, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(49, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(50, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(51, 'Cleaning rag', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(52, 'Detergent', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(53, 'Disinfectant', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(54, 'Mop', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(55, 'Plastic bucket', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(56, 'Scrub brush', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(57, 'Dust mask', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(58, 'Broom', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(59, 'Hammer', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(60, 'Skillsaw', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(61, 'Prybar', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(62, 'Shovel', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(63, 'Flashlight', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(64, 'Duct tape', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(65, 'Underwear', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(66, 'Socks', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(67, 'Warm Jacket', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(68, 'Raincoat', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(69, 'Gloves', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(70, 'Pants', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(71, 'Boots', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(72, 'Dishes', 24, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(73, 'Pots', 24, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(74, 'Paring knives', 24, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(75, 'Pan', 24, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(76, 'Glass', 24, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(77, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(78, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(79, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(80, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(81, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(82, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"ghw56\",\"detail_value\":\"twhwhrwh\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(83, 't22', 9, '[{\"detail_name\":\"wtwty\",\"detail_value\":\"wytwty\"}]', 0),
(84, 'water ', 6, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(85, 'Coca Cola', 6, '[{\"detail_name\":\"Volume\",\"detail_value\":\"500ml\"}]', 0),
(86, 'spray', 26, '[{\"detail_name\":\"volume\",\"detail_value\":\"75ml\"}]', 0),
(87, 'Outdoor spiral', 26, '[{\"detail_name\":\"duration\",\"detail_value\":\"7 hours\"}]', 0),
(88, 'Baby bottle', 25, '[{\"detail_name\":\"volume\",\"detail_value\":\"250ml\"}]', 0),
(89, 'Pacifier', 25, '[{\"detail_name\":\"material\",\"detail_value\":\"silicone\"}]', 0),
(90, 'Condensed milk', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"400gr\"}]', 0),
(91, 'Cereal bar', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"23,5gr\"}]', 0),
(92, 'Pocket Knife', 23, '[{\"detail_name\":\"Number of different tools\",\"detail_value\":\"3\"},{\"detail_name\":\"Tool\",\"detail_value\":\"Knife\"},{\"detail_name\":\"Tool\",\"detail_value\":\"Screwdriver\"},{\"detail_name\":\"Tool\",\"detail_value\":\"Spoon\"}]', 0),
(93, 'Water Disinfection Tablets', 16, '[{\"detail_name\":\"Basic Ingredients\",\"detail_value\":\"Iodine\"},{\"detail_name\":\"Suggested for\",\"detail_value\":\"Everyone expept pregnant women\"}]', 0),
(94, 'Radio', 27, '[{\"detail_name\":\"Power\",\"detail_value\":\"Batteries\"},{\"detail_name\":\"Frequencies Range\",\"detail_value\":\"3 kHz - 3000 GHz\"}]', 0),
(95, 'Kitchen appliances', 14, '[{\"detail_name\":\"\",\"detail_value\":\"(scrubbers, rubber gloves, kitchen detergent, laundry soap)\"}]', 0),
(96, 'Winter hat', 28, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(97, 'Winter gloves', 28, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(98, 'Scarf', 28, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(99, 'Thermos', 28, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(100, 'Tea', 6, '[{\"detail_name\":\"volume\",\"detail_value\":\"500ml\"}]', 0),
(101, 'Dog Food ', 29, '[{\"detail_name\":\"volume\",\"detail_value\":\"500g\"}]', 0),
(102, 'Cat Food', 29, '[{\"detail_name\":\"volume\",\"detail_value\":\"500g\"}]', 0),
(103, 'Canned', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(104, 'Chlorine', 22, '[{\"detail_name\":\"volume\",\"detail_value\":\"500ml\"}]', 0),
(105, 'Medical gloves', 22, '[{\"detail_name\":\"volume\",\"detail_value\":\"20pieces\"}]', 0),
(106, 'T-Shirt', 7, '[{\"detail_name\":\"size\",\"detail_value\":\"XL\"}]', 0),
(107, 'Cooling Fan', 34, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(108, 'Cool Scarf', 34, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(109, 'Whistle', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(110, 'Blankets', 28, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(111, 'Sleeping Bag', 28, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(112, 'Toothbrush', 21, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(113, 'Toothpaste', 21, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(114, 'Thermometer', 16, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(115, 'Rice', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(116, 'Bread', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(117, 'Towels', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(118, 'Wet Wipes', 22, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(119, 'Fire Extinguisher', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(120, 'Fruits', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(121, 'Duct Tape', 23, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(122, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(123, 'Αθλητικά', 19, '[{\"detail_name\":\"\\u039d\\u03bf 46\",\"detail_value\":\"\"}]', 0),
(124, 'Πασατέμπος', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(125, 'Bandages', 35, '[{\"detail_name\":\"Adhesive\",\"detail_value\":\"2 meters\"}]', 0),
(126, 'Betadine', 35, '[{\"detail_name\":\"Povidone iodine 10%\",\"detail_value\":\"240 ml\"}]', 0),
(127, 'cotton wool', 35, '[{\"detail_name\":\"100% Hydrofile\",\"detail_value\":\"70gr\"}]', 0),
(128, 'Crackers', 5, '[{\"detail_name\":\"Quantity per package\",\"detail_value\":\"10\"},{\"detail_name\":\"Packages\",\"detail_value\":\"2\"}]', 0),
(129, 'Sanitary Pads', 21, '[{\"detail_name\":\"piece\",\"detail_value\":\"10 pieces\"},{\"detail_name\":\"\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(130, 'Sanitary wipes', 21, '[{\"detail_name\":\"pank\",\"detail_value\":\"10 packs\"}]', 0),
(131, 'Electrolytes', 16, '[{\"detail_name\":\"packet of pills\",\"detail_value\":\"20 pills\"}]', 0),
(132, 'Pain killers', 16, '[{\"detail_name\":\"packet of pills\",\"detail_value\":\"20 pills\"}]', 0),
(133, 'Flashlight', 23, '[{\"detail_name\":\"pieces\",\"detail_value\":\"1\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(134, 'Juice', 6, '[{\"detail_name\":\"volume\",\"detail_value\":\"500ml\"}]', 0),
(135, 'Toilet Paper', 21, '[{\"detail_name\":\"rolls\",\"detail_value\":\"1 roll\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(136, 'Sterilized Saline', 16, '[{\"detail_name\":\"volume\",\"detail_value\":\"100ml\"}]', 0),
(137, 'Biscuits', 5, '[{\"detail_name\":\"packet\",\"detail_value\":\"1 packet\"}]', 0),
(138, 'Antihistamines', 16, '[{\"detail_name\":\"pills\",\"detail_value\":\"10 pills\"}]', 0),
(139, 'Instant Pancake Mix', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(140, 'Lacta', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"105g\"}]', 0),
(141, 'Canned Tuna', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(142, 'Batteries', 23, '[{\"detail_name\":\"6 pack\",\"detail_value\":\"\"}]', 0),
(143, 'Dust Mask', 35, '[{\"detail_name\":\"1\",\"detail_value\":\"\"}]', 0),
(144, 'Can Opener', 23, '[{\"detail_name\":\"1\",\"detail_value\":\"\"}]', 0),
(145, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(146, 'Πατατάκια', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"45g\"}]', 0),
(147, 'Σερβιέτες', 21, '[{\"detail_name\":\"pcs\",\"detail_value\":\"18\"}]', 0),
(148, 'Dry Cranberries', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"100\"}]', 0),
(149, 'Dry Apricots', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"100\"}]', 0),
(150, 'Dry Figs', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"100\"}]', 0),
(151, 'Παξιμάδια', 5, '[{\"detail_name\":\"weight\",\"detail_value\":\"200g\"}]', 0),
(152, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(153, 'Test Item', 11, '[{\"detail_name\":\"volume\",\"detail_value\":\"200g\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(154, 'Painkillers', 35, '[{\"detail_name\":\"Potency\",\"detail_value\":\"High\"}]', 0),
(155, 'Tampons', 16, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(156, 'plaster set', 41, '[{\"detail_name\":\"1\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(157, 'elastic bandages', 41, '[{\"detail_name\":\"\",\"detail_value\":\"12\"}]', 0),
(158, 'traumaplast', 41, '[{\"detail_name\":\"\",\"detail_value\":\"20\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(159, 'thermal blanket', 41, '[{\"detail_name\":\"\",\"detail_value\":\"2\"}]', 0),
(160, 'burn gel', 41, '[{\"detail_name\":\"ml\",\"detail_value\":\"500\"}]', 0),
(161, 'pet carrier', 41, '[{\"detail_name\":\"\",\"detail_value\":\"2\"}]', 0),
(162, 'pet dishes', 41, '[{\"detail_name\":\"\",\"detail_value\":\"10\"}]', 0),
(163, 'plastic bags', 41, '[{\"detail_name\":\"\",\"detail_value\":\"20\"}]', 0),
(164, 'toys', 41, '[{\"detail_name\":\"\",\"detail_value\":\"5\"}]', 0),
(165, 'burn pads', 41, '[{\"detail_name\":\"\",\"detail_value\":\"5\"}]', 0),
(166, 'cheese', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"1000\"}]', 0),
(167, 'lettuce', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"500\"}]', 0),
(168, 'eggs', 5, '[{\"detail_name\":\"pair\",\"detail_value\":\"10\"}]', 0),
(169, 'steaks', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"1000\"}]', 0),
(170, 'beef burgers', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"500\"}]', 0),
(171, 'tomatoes', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"1000\"}]', 0),
(172, 'onions', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"500\"}]', 0),
(173, 'flour', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"1000\"}]', 0),
(174, 'pastel', 5, '[{\"detail_name\":\"\",\"detail_value\":\"7\"}]', 0),
(175, 'nuts', 5, '[{\"detail_name\":\"grams\",\"detail_value\":\"500\"}]', 0),
(176, 'dramamines', 42, '[{\"detail_name\":\"\",\"detail_value\":\"5\"}]', 0),
(177, 'nurofen', 42, '[{\"detail_name\":\"\",\"detail_value\":\"10\"}]', 0),
(178, 'imodium', 42, '[{\"detail_name\":\"\",\"detail_value\":\"5\"}]', 0),
(179, 'emetostop', 42, '[{\"detail_name\":\"\",\"detail_value\":\"5\"}]', 0),
(180, 'xanax', 42, '[{\"detail_name\":\"\",\"detail_value\":\"5\"}]', 0),
(181, 'saflutan', 42, '[{\"detail_name\":\"\",\"detail_value\":\"2\"}]', 0),
(182, 'sadolin', 42, '[{\"detail_name\":\"\",\"detail_value\":\"3\"}]', 0),
(183, 'depon', 42, '[{\"detail_name\":\"\",\"detail_value\":\"20\"}]', 0),
(184, 'panadol', 42, '[{\"detail_name\":\"\",\"detail_value\":\"6\"}]', 0),
(185, 'ponstan ', 42, '[{\"detail_name\":\"\",\"detail_value\":\"10\"}]', 0),
(186, 'algofren', 42, '[{\"detail_name\":\"10\",\"detail_value\":\"600ml\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(187, 'effervescent depon', 42, '[{\"detail_name\":\"67\",\"detail_value\":\"1000mg\"}]', 0),
(188, 'cold coffee', 6, '[{\"detail_name\":\"10\",\"detail_value\":\"330ml\"}]', 0),
(189, 'Hell', 43, '[{\"detail_name\":\"22\",\"detail_value\":\"330\"}]', 0),
(190, 'Monster', 43, '[{\"detail_name\":\"31\",\"detail_value\":\"500ml\"}]', 0),
(191, 'Redbull', 43, '[{\"detail_name\":\"40\",\"detail_value\":\"330ml\"}]', 0),
(192, 'Powerade', 43, '[{\"detail_name\":\"23\",\"detail_value\":\"500ml\"}]', 0),
(193, 'PRIME', 43, '[{\"detail_name\":\"15\",\"detail_value\":\"500ml\"}]', 0),
(194, 'Lighter', 23, '[{\"detail_name\":\"16\",\"detail_value\":\"Mini\"}]', 0),
(195, 'isothermally shirts', 28, '[{\"detail_name\":\"5\",\"detail_value\":\"Medium\"},{\"detail_name\":\"6\",\"detail_value\":\"Large\"},{\"detail_name\":\"10\",\"detail_value\":\"Small\"},{\"detail_name\":\"2\",\"detail_value\":\"XL\"}]', 0),
(196, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(197, 'Depon', 42, '[{\"detail_name\":\"10\",\"detail_value\":\"500mg\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(198, 'Shorts', 34, '[{\"detail_name\":\"20\",\"detail_value\":\"\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(199, 'Chicken', 5, '[{\"detail_name\":\"5\",\"detail_value\":\"1.5kg\"}]', 0),
(200, 'Toilet Paper', 21, '[{\"detail_name\":\"20\",\"detail_value\":\"200g\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(201, 'toys', 41, '[{\"detail_name\":\"30\",\"detail_value\":\"\"}]', 0),
(202, 'sanitary napkins', 21, '[{\"detail_name\":\"30\",\"detail_value\":\"500g\"}]', 0),
(203, 'COVID-19 Tests', 16, '[{\"detail_name\":\"20\",\"detail_value\":\"\"}]', 0),
(204, 'Club Soda', 6, '[{\"detail_name\":\"volume\",\"detail_value\":\"500ml\"}]', 0),
(205, 'Wheelchairs', 44, '[{\"detail_name\":\"quantity\",\"detail_value\":\"100\"}]', 0),
(206, 'mobile phones', 45, '[{\"detail_name\":\"iphone\",\"detail_value\":\"200\"}]', 0),
(207, 'spoon', 24, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(208, 'fork', 24, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(209, 'MOTOTRBO R7', 45, '[{\"detail_name\":\"band\",\"detail_value\":\"UHF\\/VHF\"},{\"detail_name\":\"Wi-Fi\",\"detail_value\":\"2,4\\/5,0 GHz\"},{\"detail_name\":\"Bluetooth\",\"detail_value\":\"5.2\"},{\"detail_name\":\"\\u039f\\u03b8\\u03cc\\u03bd\\u03b7\",\"detail_value\":\"2,4\\u201d 320 x 240 px. QVGA\"},{\"detail_name\":\"\\u03b4\\u03b9\\u03ac\\u03c1\\u03ba\\u03b5\\u03b9\\u03b1 \\u03b6\\u03c9\\u03ae\\u03c2 \\u03c4\\u03b7\\u03c2 \\u03bc\\u03c0\\u03b1\\u03c4\\u03b1\\u03c1\\u03af\\u03b1\\u03c2\",\"detail_value\":\"28 \\u03ce\\u03c1\\u03b5\\u03c2\"}]', 0),
(210, 'RM LA 250 (VHF Linear Ενισχυτής 140-150MHz)', 45, '[{\"detail_name\":\"Frequency\",\"detail_value\":\"140-150Mhz\"},{\"detail_name\":\"Power Supply\",\"detail_value\":\"13VDC \\/- 1V 40A\"},{\"detail_name\":\"Output RF Power (Nominal)\",\"detail_value\":\"30 \\u2013 210W ; 230W max AM\\/FM\\/CW\"},{\"detail_name\":\"Modulation Types\",\"detail_value\":\"SSB,CW,AM, FM, data etc (All narrowband modes)\"}]', 0),
(211, 'Humanitarian General Purpose Tent System (HGPTS)', 47, '[{\"detail_name\":\"PART NUMBER\",\"detail_value\":\"C14Y016X016-T\"},{\"detail_name\":\"CONTRACTOR NAME:\",\"detail_value\":\"CELINA Tent, Inc\"},{\"detail_name\":\"COLOR\",\"detail_value\":\"Tan\"},{\"detail_name\":\"SET-UP TIME\\/NUMBER OF PERSONS\",\"detail_value\":\"4 People\\/30 Minutes\"}]', 0),
(212, 'CELINA Dynamic Small Shelter ', 47, '[{\"detail_name\":\"dimensions\",\"detail_value\":\" 20\\u2019x32.5\\u2019\"},{\"detail_name\":\"TYPE\",\"detail_value\":\"Frame Structure, Expandable, Air-Transportable\"},{\"detail_name\":\"WEIGHT\",\"detail_value\":\"1,200 lbs\"}]', 0),
(213, 'Multi-purpose Area Shelter System, Type-I', 47, '[{\"detail_name\":\"TYPE\",\"detail_value\":\"Frame Structure, Expandable, Air- Transportable\"},{\"detail_name\":\"DIMENSIONS\",\"detail_value\":\"E I-40\\u2019x80\\u2019\"},{\"detail_name\":\"WEIGHT\",\"detail_value\":\"24,000 lbs\"}]', 0),
(214, 'Trousers', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(215, 'Shoes', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(216, 'Hoodie', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(217, '', 10, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(218, 'dog food', 49, '[{\"detail_name\":\"weight\",\"detail_value\":\"1k\"}]', 0),
(219, 'cat food', 49, '[{\"detail_name\":\"weight\",\"detail_value\":\"1k\"}]', 0),
(220, 'macaroni', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(221, 'rice', 5, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(222, 'scarf', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(223, 'gloves', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(224, 'underwear', 7, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(225, 'Silver blanket', 50, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(226, 'Helmet', 50, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(227, 'Disposable toilet', 50, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(228, 'Self-generated flashlight', 50, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(229, 'Mattresses ', 51, '[{\"detail_name\":\"size\",\"detail_value\":\"1.90X60\"}]', 0),
(230, 'flashlight', 51, '[{\"detail_name\":\"light\",\"detail_value\":\"blue\"}]', 0),
(231, 'matches', 51, '[{\"detail_name\":\"pack\",\"detail_value\":\"60\"}]', 0),
(232, 'Heater', 51, '[{\"detail_name\":\"Volts\",\"detail_value\":\"208\"}]', 0),
(233, 'Earplugs', 51, '[{\"detail_name\":\"material\",\"detail_value\":\"plastic\"}]', 0),
(234, 'Compass', 52, '[{\"detail_name\":\"Type\",\"detail_value\":\"Digital\"}]', 0),
(235, 'Map', 52, '[{\"detail_name\":\"Material\",\"detail_value\":\"Paper\"}]', 0),
(236, 'GPS', 52, '[{\"detail_name\":\"Type\",\"detail_value\":\"Waterproof\"}]', 0),
(237, 'First Aid', 16, '[{\"detail_name\":\"1\",\"detail_value\":\"1\"},{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(238, 'Bandage', 16, '[{\"detail_name\":\"\",\"detail_value\":\"5\"}]', 0),
(239, 'Mask', 16, '[{\"detail_name\":\"\",\"detail_value\":\"10\"}]', 0),
(240, 'Medicines', 16, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(241, 'Water', 5, '[{\"detail_name\":\"6\",\"detail_value\":\"1500ml\"}]', 0),
(242, 'Canned Goods', 5, '[{\"detail_name\":\"2\",\"detail_value\":\"80g\"}]', 0),
(243, 'Snacks', 5, '[{\"detail_name\":\"3\",\"detail_value\":\"100g\"}]', 0),
(244, 'Cereals', 5, '[{\"detail_name\":\"1\",\"detail_value\":\"800g\"}]', 0),
(245, 'Blankets', 53, '[{\"detail_name\":\"1\",\"detail_value\":\"\"}]', 0),
(246, 'Shirt', 53, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(247, 'Pants', 53, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(248, 'Shoes', 53, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(249, 'Socks', 53, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(250, 'Caps', 53, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(251, 'Gloves', 53, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(252, 'Flashlight', 54, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(253, 'Batteries', 54, '[{\"detail_name\":\"AAA\",\"detail_value\":\"5\"}]', 0),
(254, 'Repair Tools', 54, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(255, 'Soap and Shampoo', 21, '[{\"detail_name\":\"1\",\"detail_value\":\"200ml\"}]', 0),
(256, 'Toothpastes and Toothbrushes', 21, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(257, 'Towels', 21, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(258, 'Diapers', 56, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(259, 'Animal food', 56, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(260, 'Pots', 57, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(261, 'Plates', 57, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(262, 'Cups', 57, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(263, 'Cutlery ', 57, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(264, 'Cleaning Supplies', 57, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(265, 'Kitchen Appliances', 57, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0),
(266, 'Home Repair Tools', 57, '[{\"detail_name\":\"\",\"detail_value\":\"\"}]', 0);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `offers`
--

CREATE TABLE `offers` (
  `id` int(255) NOT NULL,
  `civilian_id` int(255) NOT NULL,
  `date` datetime NOT NULL,
  `item_id` varchar(255) NOT NULL,
  `quantity` int(255) NOT NULL,
  `load_date` datetime NOT NULL,
  `rescuer_id` int(255) NOT NULL,
  `completed` int(1) NOT NULL,
  `complete_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `offers`
--

INSERT INTO `offers` (`id`, `civilian_id`, `date`, `item_id`, `quantity`, `load_date`, `rescuer_id`, `completed`, `complete_date`) VALUES
(1, 6, '2024-05-25 19:29:19', '17', 2, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00'),
(13, 3, '2024-06-03 16:39:18', '16', 3, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `requests`
--

CREATE TABLE `requests` (
  `id` int(255) NOT NULL,
  `civilian_id` int(255) NOT NULL,
  `date` datetime NOT NULL,
  `item_id` varchar(255) NOT NULL,
  `quantity` int(255) NOT NULL,
  `load_date` datetime NOT NULL,
  `rescuer_id` int(255) NOT NULL,
  `completed` int(1) NOT NULL,
  `complete_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `requests`
--

INSERT INTO `requests` (`id`, `civilian_id`, `date`, `item_id`, `quantity`, `load_date`, `rescuer_id`, `completed`, `complete_date`) VALUES
(9, 3, '2024-06-03 17:24:25', '20', 3, '2024-06-07 16:26:28', 2, 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `phone` bigint(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'civilian'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `surname`, `phone`, `latitude`, `longitude`, `password`, `user_type`) VALUES
(1, 'admin', 'Admin', 'Adminas', 6912345678, '38.246279', '21.735030', '21232f297a57a5a743894a0e4a801fc3', 'admin'),
(2, 'r1', 'r1', 'r1', 1234567890, '38.24694357455419', '21.73662197444646', '202cb962ac59075b964b07152d234b70', 'rescuer'),
(3, 'c1', 'Civi', 'Lian', 2610123456, '38.220588', '21.731126', '202cb962ac59075b964b07152d234b70', 'civilian'),
(4, 'r2', 'r2', 'r2', 6912324458, '38.241835', '21.744357', '202cb962ac59075b964b07152d234b70', 'rescuer'),
(5, 'r3', 'r3', 'r3', 6912345678, '38.255442', '21.741126', '202cb962ac59075b964b07152d234b70', 'rescuer'),
(6, 'c2', 'Paul', 'Itis', 6998765432, '38.242468', '21.734958', '202cb962ac59075b964b07152d234b70', 'civilian');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT για πίνακα `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT για πίνακα `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT για πίνακα `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT για πίνακα `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
