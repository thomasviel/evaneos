# Explanation

- At first, I wanted to launch the example.php file. To do this I needed to install Composer and I also took the opportunity to update the project to a recent version of php. I did not use php8 because the latest fzaninotto/faker is only compatible with versions up to php7.

- Once I was able to run the example, I tried launching the unit test. The test did not pass so I looked at the code in TemplateManager in order to correct the test. Then I improved the test suite to cover all of the functionalities and to avoid regression due to my refactoring.

- Seeing singletons in the code tempted me to migrate to Symfony to be able to use Dependency Injections. However, according to the instructions given, the TemplateManager::getTemplateComputed method is called everywhere and so changing how this method is called would probably not have been feasible. For the same reason, I unfortunately didn't feel able to add the psr4 autoloader, so I couldn't use namespaces and had to stick to the require_once expression.

- Before starting to refactor, I added dependencies that allowed me to detect problems and respect php coding standards : php-cs-fixer, php_codesniffer and phpstan.

- I then typed the fields and removed the Singleton anti pattern. Ideally, I would have preferred to use Dependency Injection and Symfony.

- I wanted to create a generic mechanism for developers working on the code in the future to be able to easily add new placeholders. Adding a class which only defines the text to be replaced seemed to be a logical idea. The problem was detecting the instances of an interface without using Symfony. With Symfony, I could have tagged the classes and retreive the objects more easily. In this case, I had to stock the class instances in an array because I didn't want to use reflection. This wasn't ideal because the classes are instantiated even if the template does not contain a corresponding placeholder, but it is more extensible than the existing code.

- I corrected the tests to mock all the non functional components and everything external to the TemplateManager, i.e. the repositories.

- Finally I made minor corrections to the project as a whole.

- I spent around six hours on this test.

## TODO

- If possible, this should be migrated onto a framework, which will handle the dependency injections.
- Psr4 should be respected and namespaces should be added onto all of the classes.
- Replace PlaceholderFactory::getInstances with Service Tags (https://symfony.com/doc/current/service_container/tags.html).
- Isolate the business code, excluding for example the repositories (hexagonal architecture).