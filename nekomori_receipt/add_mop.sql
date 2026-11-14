-- Run once on an existing nekomori_pos database
ALTER TABLE `transactions`
  ADD COLUMN `mop` varchar(30) NOT NULL DEFAULT 'Cash' AFTER `total_amount`;
