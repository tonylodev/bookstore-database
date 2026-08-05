-- ============================================
-- Online Bookstore Demo Data
-- All names, contact details, and titles below are fictional.
-- ============================================

USE OnlineBookstore;

START TRANSACTION;

INSERT INTO Subject (SubjectID, CategoryName) VALUES
    (1, 'Fiction'),
    (2, 'Technology'),
    (3, 'Science');

INSERT INTO Supplier (SupplierID, CompanyName, Phone) VALUES
    (1, 'Northstar Books', '555-0101'),
    (2, 'Circuit Press', '555-0102'),
    (3, 'Field Notes Publishing', '555-0103');

INSERT INTO Book (
    BookID,
    Title,
    Author,
    UnitPrice,
    Quantity,
    SubjectID,
    SupplierID
) VALUES
    (1, 'The Silent Harbor', 'Maya Chen', 18.99, 12, 1, 1),
    (2, 'Practical SQL Systems', 'Alex Rivera', 54.50, 8, 2, 2),
    (3, 'Graph Thinking', 'Priya Nair', 42.00, 5, 2, 2),
    (4, 'Ocean Ecology', 'Jordan Lee', 27.75, 15, 3, 3),
    (5, 'Applied Machine Learning', 'Priya Nair', 68.00, 4, 2, 2);

INSERT INTO Customer (
    CustomerID,
    FirstName,
    LastName,
    Email,
    Phone
) VALUES
    (1, 'Avery', 'Morgan', 'avery.morgan@example.test', '555-0201'),
    (2, 'Riley', 'Park', 'riley.park@example.test', '555-0202'),
    (3, 'Sam', 'Patel', 'sam.patel@example.test', '555-0203');

INSERT INTO Shipper (ShipperID, ShipperName) VALUES
    (1, 'Swift Parcel'),
    (2, 'Northwind Freight');

INSERT INTO Orders (
    OrderID,
    CustomerID,
    OrderDate,
    ShippedDate,
    ShipperID
) VALUES
    (1, 1, '2016-08-01', '2016-08-02', 1),
    (2, 2, '2016-08-03', NULL, NULL),
    (3, 1, '2016-08-04', '2016-08-05', 2);

INSERT INTO OrderDetail (
    OrderDetailID,
    OrderID,
    BookID,
    Quantity
) VALUES
    (1, 1, 2, 1),
    (2, 1, 4, 2),
    (3, 2, 5, 1),
    (4, 3, 1, 3),
    (5, 3, 3, 1);

COMMIT;
