# Project Name

## What is Project
Market-App is a dynamic web application. It is a group project through CTIS256 course.

## Objective
The main objective of [Market-App] is to create a market-customer system that facilitate to sell products that are nearing their expiration date at a lower price than their normal price..

## Features
### For Market
- Register to the system
- Update/edit own information
- CRUD (Create/Read/Update/Delete) of own products

### For Customer
- Register to the system
- Update/edit own information
- Search products with keywords (searchbar)
- See products that are near to the customer (district and city precedence)
- Session-based shopping card system to buy products, purchase/delete actions on shopping card

## Non-functional Requirements
- **Usability**: Sticky-form functionality, form validation, Email verification (PHPMailer)
- **Security**: CSRF, XSS, SQL Injection preventions. Non-empty input parameters.
- **UI**: Bootstrap, Free CSS UIs, HTML Form Template

## Technologies Used
- PHP
- MySQL
- Apache Web Server
- CSS
- HTML
- Git
- WAMP

## Installation
1. Clone the repository: `git clone [repository URL]`
2. Install dependencies: `composer require phpmailer/phpmailer`
3. Config/config.php: Inside config.php file, add const EMAIL and PASSWORD variable to your mail account.
  - Also set proper SMTP server for your mail account

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request with your changes.
