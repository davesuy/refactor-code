Refactored Code
=================
app/Http/Controllers/BookingController.php
app/Contracts/BaseUser.php
app/Contracts/UserInterface.php
app/Factory/UserFactory.php
App/Models/Customer.php
App/Models/Translator.php
app/Repository/BookingRepository.php

Added a Test Unit
=====================
3) App/tests/Unit/TeHelperTest.php method willExpireAt


## My thoughts

There are several if-else conditions for the Customer and Translator user objects. I created a factory design pattern to simplify the `BookingRepository` class instead of having multiple else conditions. I created child and parent classes to simplify the process, following the contract class which is an interface.


## What makes this a terrible code?

Too many if else conditions or statement.

## What makes this an amazing code?

This code follows the repository pattern, which separates hard dependencies of models from controllers. This ensures a clear separation of concerns: models represent only tables or objects and other data structures without the responsibility of communicating with or extracting data from the database.




