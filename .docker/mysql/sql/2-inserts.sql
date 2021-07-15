INSERT INTO t_money_transfer.users
(id, full_name, email, password, registration_number, shopkeeper, created_at, updated_at)
VALUES('5d541b1b-e038-41dd-96d1-8b757b4d23f0', 'John Doe', 'john.doe@test.com', '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa', '76997937063', 0, now(), now());

INSERT INTO t_money_transfer.wallets
(id, user_id, balance, created_at, updated_at)
VALUES('91e92c5f-d9d0-437a-9435-58839fdbb6c5', '5d541b1b-e038-41dd-96d1-8b757b4d23f0', 1000, now(), now());

INSERT INTO t_money_transfer.users
(id, full_name, email, password, registration_number, shopkeeper, created_at, updated_at)
VALUES('5f97a1d3-a2e2-4a58-aa48-66cd49ab14a8', 'Bob Dylan', 'bob.dylan@test.com', '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa', '08087648021', 0, now(), now());

INSERT INTO t_money_transfer.wallets
(id, user_id, balance, created_at, updated_at)
VALUES('b786b829-2a9a-4454-af52-b06a552d845c', '5f97a1d3-a2e2-4a58-aa48-66cd49ab14a8', 100, now(), now());

INSERT INTO t_money_transfer.users
(id, full_name, email, password, registration_number, shopkeeper, created_at, updated_at)
VALUES('1084fe32-5a84-4ce4-8ec0-3cbbe4485863', 'John Dealer', 'john.dealer@test.com', '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa', '39382854000115', 1, now(), now());

INSERT INTO t_money_transfer.wallets
(id, user_id, balance, created_at, updated_at)
VALUES('9442fd46-44cf-4571-9bfd-59670b765719', '1084fe32-5a84-4ce4-8ec0-3cbbe4485863', 10000, now(), now());

INSERT INTO t_money_transfer.users
(id, full_name, email, password, registration_number, shopkeeper, created_at, updated_at)
VALUES('92ffc998-5e2a-488f-ba43-76f0467c7f6f', 'Bob Dealer', 'bob.dealer@test.com', '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa', '64041937000198', 1, now(), now());

INSERT INTO t_money_transfer.wallets
(id, user_id, balance, created_at, updated_at)
VALUES('90e75b1a-ae80-4c28-b1c8-7f06d35e7f60', '92ffc998-5e2a-488f-ba43-76f0467c7f6f', 50000, now(), now());
