#1
SELECT * FROM Customer;

#2
SELECT * FROM Book WHERE UnitPrice > 50;

#3
SELECT Title, UnitPrice FROM Book ORDER BY UnitPrice DESC;

#4
SELECT DISTINCT Author FROM Book;

#5
SELECT CustomerID, COUNT(*) AS OrderCount
FROM Orders
GROUP BY CustomerID;

#6
SELECT SupplierID, COUNT(*) AS BookCount
FROM Book
GROUP BY SupplierID;

#7
SELECT BookID, SUM(Quantity) AS TotalSold
FROM OrderDetail
GROUP BY BookID;

#8
SELECT Book.Title, Supplier.CompanyName
FROM Book
JOIN Supplier ON Book.SupplierID = Supplier.SupplierID;

#9
SELECT Customer.FirstName, Customer.LastName, Orders.OrderDate
FROM Customer
JOIN Orders ON Customer.CustomerID = Orders.CustomerID
WHERE Orders.ShippedDate IS NULL;

#10
SELECT Subject.CategoryName, COUNT(Book.BookID) AS TotalBooks
FROM Subject
LEFT JOIN Book ON Subject.SubjectID = Book.SubjectID
GROUP BY Subject.CategoryName;

#11
SELECT Book.Title, Book.UnitPrice
FROM Book
WHERE Book.UnitPrice = (SELECT MAX(UnitPrice) FROM Book);

#12
SELECT Book.Title, OrderDetail.Quantity
FROM Book
JOIN OrderDetail ON Book.BookID = OrderDetail.BookID
ORDER BY OrderDetail.Quantity DESC;

#13
SELECT Orders.OrderID, SUM(Book.UnitPrice * OrderDetail.Quantity) AS OrderTotal
FROM Orders
JOIN OrderDetail ON Orders.OrderID = OrderDetail.OrderID
JOIN Book ON OrderDetail.BookID = Book.BookID
GROUP BY Orders.OrderID;

#14
SELECT Author, AVG(UnitPrice) AS AvgPrice
FROM Book
GROUP BY Author;

#15
SELECT Book.Title, Supplier.Phone
FROM Book
JOIN Supplier ON Book.SupplierID = Supplier.SupplierID;

#16
SELECT OrderID
FROM Orders
WHERE OrderDate BETWEEN '2016-08-01' AND '2016-08-04';

#17
SELECT Shipper.ShpperName, COUNT(Orders.OrderID) AS TotalShipped
FROM Shipper
LEFT JOIN Orders ON Shipper.ShipperID = Orders.ShipperID
GROUP BY Shipper.ShpperName;

#18
SELECT Book.Title
FROM Book
WHERE Quantity < 10;

#19
SELECT Customer.FirstName, Customer.LastName, SUM(Book.UnitPrice * OrderDetail.Quantity) AS TotalSpent
FROM Customer
JOIN Orders ON Customer.CustomerID = Orders.CustomerID
JOIN OrderDetail ON Orders.OrderID = OrderDetail.OrderID
JOIN Book ON OrderDetail.BookID = Book.BookID
GROUP BY Customer.CustomerID;
