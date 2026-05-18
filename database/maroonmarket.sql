-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 06:15 PM
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
-- Database: `maroonmarket`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrow_listings`
--

CREATE TABLE `borrow_listings` (
  `listingID` int(11) NOT NULL,
  `maxDays` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cartID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cartItemID` int(11) NOT NULL,
  `cartID` int(11) NOT NULL,
  `listingID` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `fineID` int(11) NOT NULL,
  `transactionID` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `isPaid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `itemID` int(11) NOT NULL,
  `ownerID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `itemCondition` enum('new','good','fair','poor') NOT NULL,
  `source` enum('student','lost_and_found') DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`itemID`, `ownerID`, `name`, `description`, `category`, `itemCondition`, `source`) VALUES
(1, 2, 'Stapler', 'Black Stapler', 'school supplies', 'new', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `listingID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  `datePosted` date NOT NULL DEFAULT curdate(),
  `listingType` enum('sale','rent','borrow') NOT NULL,
  `listingStatus` enum('active','closed','pending','deleted') DEFAULT 'active',
  `location` varchar(100) DEFAULT NULL,
  `availabilityFrom` date DEFAULT NULL,
  `availabilityTo` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`listingID`, `itemID`, `datePosted`, `listingType`, `listingStatus`, `location`, `availabilityFrom`, `availabilityTo`) VALUES
(1, 1, '2026-05-18', 'sale', 'active', 'NGE Building', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `listing_images`
--

CREATE TABLE `listing_images` (
  `imageID` int(11) NOT NULL,
  `listingID` int(11) NOT NULL,
  `imagePath` varchar(500) NOT NULL,
  `sortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing_images`
--

INSERT INTO `listing_images` (`imageID`, `listingID`, `imagePath`, `sortOrder`) VALUES
(1, 1, 'uploads/1779119407_6a0b352f89888_stapler.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `listing_reports`
--

CREATE TABLE `listing_reports` (
  `reportID` int(11) NOT NULL,
  `listingID` int(11) DEFAULT NULL,
  `reporterID` int(11) DEFAULT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `status` enum('open','resolved','dismissed') DEFAULT 'open',
  `moderatorNote` varchar(500) DEFAULT NULL,
  `resolvedAt` datetime DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listing_submissions`
--

CREATE TABLE `listing_submissions` (
  `submissionID` int(11) NOT NULL,
  `submitterID` int(11) NOT NULL,
  `listingType` enum('sale','rent','borrow') NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `itemCondition` enum('new','good','fair','poor') NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `rentalFee` decimal(10,2) DEFAULT NULL,
  `rentalPricePerDay` decimal(10,2) DEFAULT NULL,
  `depositAmount` decimal(10,2) DEFAULT NULL,
  `maxDays` int(11) DEFAULT NULL,
  `availabilityFrom` date DEFAULT NULL,
  `availabilityTo` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewerID` int(11) DEFAULT NULL,
  `reviewedAt` datetime DEFAULT NULL,
  `reviewNote` varchar(500) DEFAULT NULL,
  `approvedListingID` int(11) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing_submissions`
--

INSERT INTO `listing_submissions` (`submissionID`, `submitterID`, `listingType`, `title`, `category`, `description`, `itemCondition`, `location`, `price`, `rentalFee`, `rentalPricePerDay`, `depositAmount`, `maxDays`, `availabilityFrom`, `availabilityTo`, `status`, `reviewerID`, `reviewedAt`, `reviewNote`, `approvedListingID`, `createdAt`) VALUES
(1, 2, 'sale', 'Stapler', 'school supplies', 'Black Stapler', 'new', 'NGE Building', 150.00, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', 1, '2026-05-18 17:50:30', NULL, 1, '2026-05-18 15:50:07');

-- --------------------------------------------------------

--
-- Table structure for table `listing_submission_images`
--

CREATE TABLE `listing_submission_images` (
  `imageID` int(11) NOT NULL,
  `submissionID` int(11) NOT NULL,
  `imagePath` varchar(500) NOT NULL,
  `sortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing_submission_images`
--

INSERT INTO `listing_submission_images` (`imageID`, `submissionID`, `imagePath`, `sortOrder`) VALUES
(1, 1, 'uploads/1779119407_6a0b352f89888_stapler.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rental_listings`
--

CREATE TABLE `rental_listings` (
  `listingID` int(11) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) NOT NULL,
  `rentalPricePerDay` decimal(10,2) NOT NULL,
  `maxDays` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returnable_transactions`
--

CREATE TABLE `returnable_transactions` (
  `transactionID` int(11) NOT NULL,
  `dueDate` date NOT NULL,
  `returnDate` date DEFAULT NULL,
  `returnCondition` enum('good','damaged','lost') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_listings`
--

CREATE TABLE `sale_listings` (
  `listingID` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_listings`
--

INSERT INTO `sale_listings` (`listingID`, `price`) VALUES
(1, 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `sale_transactions`
--

CREATE TABLE `sale_transactions` (
  `transactionID` int(11) NOT NULL,
  `finalPrice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_transactions`
--

INSERT INTO `sale_transactions` (`transactionID`, `finalPrice`) VALUES
(1, 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactionID` int(11) NOT NULL,
  `listingID` int(11) NOT NULL,
  `senderID` int(11) NOT NULL,
  `receiverID` int(11) NOT NULL,
  `checkoutDate` date NOT NULL DEFAULT curdate(),
  `transactionType` enum('sale','returnable') NOT NULL,
  `status` enum('pending','active','approved','completed','returned','cancelled','rejected') DEFAULT 'pending',
  `amount` decimal(10,2) DEFAULT 0.00,
  `startDate` date DEFAULT NULL,
  `dueDate` date DEFAULT NULL,
  `returnedDate` date DEFAULT NULL,
  `returnCondition` varchar(100) DEFAULT NULL,
  `fineAmount` decimal(10,2) DEFAULT 0.00,
  `fineNote` varchar(255) DEFAULT NULL,
  `isFinePaid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transactionID`, `listingID`, `senderID`, `receiverID`, `checkoutDate`, `transactionType`, `status`, `amount`, `startDate`, `dueDate`, `returnedDate`, `returnCondition`, `fineAmount`, `fineNote`, `isFinePaid`) VALUES
(1, 1, 3, 2, '2026-05-18', 'sale', 'approved', 150.00, NULL, NULL, NULL, NULL, 0.00, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_reviews`
--

CREATE TABLE `transaction_reviews` (
  `reviewID` int(11) NOT NULL,
  `transactionID` int(11) NOT NULL,
  `reviewerID` int(11) NOT NULL,
  `revieweeID` int(11) NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `contactNumber` varchar(20) DEFAULT NULL,
  `role` enum('student','faculty','staff','admin') NOT NULL DEFAULT 'student',
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `firstName`, `lastName`, `email`, `passwordHash`, `contactNumber`, `role`, `status`) VALUES
(1, 'Admin', 'Admin', 'admin@cit.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'admin', 'active'),
(2, 'Alexa', 'Baniasia', 'alexa@cit.edu', '$2y$10$fIfhKBKMTU7dviIGtcZYBOSK0yYx3siscyhKqvqJboiuFUZVYL.Rm', NULL, 'student', 'active'),
(3, 'Rea', 'Sabellita', 'rea@cit.edu', '$2y$10$/LYPBnP7moEoXCUHLXpyIunGDpvhXeoiwdxFzOAISX.hH4.2a9cRa', NULL, 'student', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrow_listings`
--
ALTER TABLE `borrow_listings`
  ADD PRIMARY KEY (`listingID`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cartID`),
  ADD UNIQUE KEY `userID` (`userID`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cartItemID`),
  ADD UNIQUE KEY `uniq_cart_listing` (`cartID`,`listingID`),
  ADD KEY `listingID` (`listingID`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`fineID`),
  ADD KEY `transactionID` (`transactionID`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`itemID`),
  ADD KEY `ownerID` (`ownerID`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`listingID`),
  ADD KEY `itemID` (`itemID`);

--
-- Indexes for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD PRIMARY KEY (`imageID`),
  ADD KEY `listingID` (`listingID`);

--
-- Indexes for table `listing_reports`
--
ALTER TABLE `listing_reports`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `listingID` (`listingID`),
  ADD KEY `reporterID` (`reporterID`);

--
-- Indexes for table `listing_submissions`
--
ALTER TABLE `listing_submissions`
  ADD PRIMARY KEY (`submissionID`),
  ADD KEY `submitterID` (`submitterID`),
  ADD KEY `reviewerID` (`reviewerID`);

--
-- Indexes for table `listing_submission_images`
--
ALTER TABLE `listing_submission_images`
  ADD PRIMARY KEY (`imageID`),
  ADD KEY `submissionID` (`submissionID`);

--
-- Indexes for table `rental_listings`
--
ALTER TABLE `rental_listings`
  ADD PRIMARY KEY (`listingID`);

--
-- Indexes for table `returnable_transactions`
--
ALTER TABLE `returnable_transactions`
  ADD PRIMARY KEY (`transactionID`);

--
-- Indexes for table `sale_listings`
--
ALTER TABLE `sale_listings`
  ADD PRIMARY KEY (`listingID`);

--
-- Indexes for table `sale_transactions`
--
ALTER TABLE `sale_transactions`
  ADD PRIMARY KEY (`transactionID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionID`),
  ADD KEY `listingID` (`listingID`),
  ADD KEY `senderID` (`senderID`),
  ADD KEY `receiverID` (`receiverID`);

--
-- Indexes for table `transaction_reviews`
--
ALTER TABLE `transaction_reviews`
  ADD PRIMARY KEY (`reviewID`),
  ADD UNIQUE KEY `uniq_tx_reviewer` (`transactionID`,`reviewerID`),
  ADD KEY `reviewerID` (`reviewerID`),
  ADD KEY `revieweeID` (`revieweeID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cartID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cartItemID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fineID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `listingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listing_images`
--
ALTER TABLE `listing_images`
  MODIFY `imageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listing_reports`
--
ALTER TABLE `listing_reports`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listing_submissions`
--
ALTER TABLE `listing_submissions`
  MODIFY `submissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listing_submission_images`
--
ALTER TABLE `listing_submission_images`
  MODIFY `imageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaction_reviews`
--
ALTER TABLE `transaction_reviews`
  MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_listings`
--
ALTER TABLE `borrow_listings`
  ADD CONSTRAINT `borrow_listings_ibfk_1` FOREIGN KEY (`listingID`) REFERENCES `listings` (`listingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cartID`) REFERENCES `carts` (`cartID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`listingID`) REFERENCES `listings` (`listingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`transactionID`) REFERENCES `returnable_transactions` (`transactionID`) ON UPDATE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`ownerID`) REFERENCES `users` (`userID`) ON UPDATE CASCADE;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`itemID`) REFERENCES `items` (`itemID`) ON UPDATE CASCADE;

--
-- Constraints for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD CONSTRAINT `listing_images_ibfk_1` FOREIGN KEY (`listingID`) REFERENCES `listings` (`listingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listing_reports`
--
ALTER TABLE `listing_reports`
  ADD CONSTRAINT `listing_reports_ibfk_1` FOREIGN KEY (`listingID`) REFERENCES `listings` (`listingID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_reports_ibfk_2` FOREIGN KEY (`reporterID`) REFERENCES `users` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `listing_submissions`
--
ALTER TABLE `listing_submissions`
  ADD CONSTRAINT `listing_submissions_ibfk_1` FOREIGN KEY (`submitterID`) REFERENCES `users` (`userID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_submissions_ibfk_2` FOREIGN KEY (`reviewerID`) REFERENCES `users` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `listing_submission_images`
--
ALTER TABLE `listing_submission_images`
  ADD CONSTRAINT `listing_submission_images_ibfk_1` FOREIGN KEY (`submissionID`) REFERENCES `listing_submissions` (`submissionID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rental_listings`
--
ALTER TABLE `rental_listings`
  ADD CONSTRAINT `rental_listings_ibfk_1` FOREIGN KEY (`listingID`) REFERENCES `listings` (`listingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `returnable_transactions`
--
ALTER TABLE `returnable_transactions`
  ADD CONSTRAINT `returnable_transactions_ibfk_1` FOREIGN KEY (`transactionID`) REFERENCES `transactions` (`transactionID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sale_listings`
--
ALTER TABLE `sale_listings`
  ADD CONSTRAINT `sale_listings_ibfk_1` FOREIGN KEY (`listingID`) REFERENCES `listings` (`listingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sale_transactions`
--
ALTER TABLE `sale_transactions`
  ADD CONSTRAINT `sale_transactions_ibfk_1` FOREIGN KEY (`transactionID`) REFERENCES `transactions` (`transactionID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`listingID`) REFERENCES `listings` (`listingID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`senderID`) REFERENCES `users` (`userID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`receiverID`) REFERENCES `users` (`userID`) ON UPDATE CASCADE;

--
-- Constraints for table `transaction_reviews`
--
ALTER TABLE `transaction_reviews`
  ADD CONSTRAINT `transaction_reviews_ibfk_1` FOREIGN KEY (`transactionID`) REFERENCES `transactions` (`transactionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_reviews_ibfk_2` FOREIGN KEY (`reviewerID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_reviews_ibfk_3` FOREIGN KEY (`revieweeID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
