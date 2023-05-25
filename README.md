## How to install

- Clone the repository
- Rename .env.example to .env
- Configure the DB credentials
- Run "composer install"
- Run "npm install"
- Run "npm run build"
- Run "php artisan migrate"
- You can not run the project using "php artisan serve" and then open the project on url (typically) http://127.0.0.1:8000
- Enjoy :)

## Run Tests
You can run the tests I wrote by starting "php artisan test".

These are the tests included:
- store cash transaction with valid data
- store cash transaction with more than five notes data
- store cash transaction with invalid banknotes
- store cash transaction with limit exceeded
- store credit card transaction with valid data
- store credit card transaction with incorrect number of digits
- store credit card transaction with incorrect starting digit
- store credit card transaction with this month expiring card
- store credit card transaction with 4 digit cvv
- store bank transaction with valid data
- store bank transaction with 8 digits account number
- store bank transaction with older date
