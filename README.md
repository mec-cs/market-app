# Market-App

## What is Project
Market-App is a dynamic web application. It is a group project through CTIS256 course.
### By ###
- Batu Uzun
- Berk Bera Özer
- Ege İliman
- Mehmet Enes Çakır

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

## Snapshots
<img src="https://github.com/mec-cs/market-app/assets/102901204/7cc272ed-f4db-4fa8-86bc-5f4a3cdd21dd" width="500" height="400"/>
<img src="https://github.com/mec-cs/market-app/assets/102901204/9d082e33-dfd0-466b-ad79-334c9eff4ba7" width="500" height="400"/>
<img src="https://github.com/mec-cs/market-app/assets/102901204/5acc400b-9750-467e-b847-49dbd4d40b7d" width="500" height="400"/>
<img src="https://github.com/mec-cs/market-app/assets/102901204/493c4732-0fda-4df0-982d-bca765715e2c" width="500" height="400"/>
<img src="https://github.com/mec-cs/market-app/assets/102901204/52812c4d-7124-4d0e-8492-92f9e6f02f1f" width="500" height="400"/>
<img src="https://github.com/mec-cs/market-app/assets/102901204/a1cac011-46e4-4a02-8196-c26ffe283b7e" width="500" height="400"/>
<img src="https://github.com/mec-cs/market-app/assets/102901204/cb76e4b5-3312-41e2-a5b8-7ad91794c1f7" width="500" height="400"/>
<img src="https://github.com/mec-cs/market-app/assets/102901204/d2e23115-c715-4fb3-9da0-f91814e73358" width="500" height="400"/>


## Contributing
Contributions are welcome! Please fork the repository and submit a pull request with your changes.
