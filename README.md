# Epignosis Academy Security

Please execute: `composer install`

We also need two create two symlinks:
- `ln -s ../vendor/twbs/bootstrap/dist bootstrap`
- `ln -s ../vendor/twbs/bootstrap-icons/font icons`

To run the project execute: `composer serve`

Then navigate directly to the examples:

- Exercise 01 (CSRF):
  - http://localhost:8000/ex01/
  - http://localhost:8000/ex01/leet
- Exercise 02 (SQL injection):
  - http://localhost:8000/ex02/
- Exercise 03 (Persistent XSS):
  - http://localhost:8000/ex03/
