-- ============================================
-- Online Bookstore Database Schema
-- Author: Tony Lo
-- Database: MySQL
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS OnlineBookstore;
USE OnlineBookstore;

-- ============================================
-- Table: Subject (Book Categories)
-- ============================================
CREATE TABLE Subject (
    SubjectID    INT PRIMARY KEY AUTO_INCREMENT,
    CategoryName VARCHAR(100) NOT NULL
);

-- ============================================
-- Table: Supplier
-- ============================================
CREATE TABLE Supplier (
    SupplierID  INT PRIMARY KEY AUTO_INCREMENT,
    CompanyName VARCHAR(150) NOT NULL,
    Phone       VARCHAR(20)
);

-- ============================================
-- Table: Book
-- ============================================
CREATE TABLE Book (
    BookID     INT PRIMARY KEY AUTO_INCREMENT,
    Title      VARCHAR(255) NOT NULL,
    Author     VARCHAR(150),
    UnitPrice  DECIMAL(10,2),
    Quantity   INT DEFAULT 0,
    SubjectID  INT,
    SupplierID INT,
    FOREIGN KEY (SubjectID)  REFERENCES Subject(SubjectID),
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID)
);

-- ============================================
-- Table: Customer
-- ============================================
CREATE TABLE Customer (
    CustomerID INT PRIMARY KEY AUTO_INCREMENT,
    FirstName  VARCHAR(100) NOT NULL,
    LastName   VARCHAR(100) NOT NULL,
    Email      VARCHAR(200),
    Phone      VARCHAR(20)
);

-- ============================================
-- Table: Shipper
-- ============================================
CREATE TABLE Shipper (
    ShipperID   INT PRIMARY KEY AUTO_INCREMENT,
    ShipperName VARCHAR(150) NOT NULL
);

-- ============================================
-- Table: Orders
-- ============================================
CREATE TABLE Orders (
    OrderID    INT PRIMARY KEY AUTO_INCREMENT,
    CustomerID INT NOT NULL,
    OrderDate  DATE,
    ShippedDate DATE,
    ShipperID  INT,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    FOREIGN KEY (ShipperID)  REFERENCES Shipper(ShipperID)
);

-- ============================================
-- Table: OrderDetail
-- ============================================
CREATE TABLE OrderDetail (
    OrderDetailID INT PRIMARY KEY AUTO_INCREMENT,
    OrderID       INT NOT NULL,
    BookID        INT NOT NULL,
    Quantity      INT DEFAULT 1,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID),
    FOREIGN KEY (BookID)  REFERENCES Book(BookID)
);
