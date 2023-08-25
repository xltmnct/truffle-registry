[Truffle](https://en.wikipedia.org/wiki/Truffle) is the diamond of the kitchen.
Millions of people hunt for truffles and thousands of restaurants compete for
the best specimens.

Our company connects truffle hunters and large restaurant chains. To do this,
hunters are provided with a form for registering found truffles. In addition,
we also cooperate with a large cultivator of homemade truffles, which shares
the register of their products with us.

To meet the needs of our customers, we have developed a service that allows
authorized truffle hunters to register their finds through a convenient API
that allows them to report the price and weight of a truffle.

In addition, we process the manufacturer's database, which is sent to us via
FTP. Unfortunately, they cannot share data only about new truffles, so they
send their full register of truffles every time. Now there are only about
10,000 units in their register, but according to their plan, aggressive 
cultivation will allow them to accumulate more than a million truffles in stock 
by the end of the year.

Our service must process data from these sources and compile a common register 
of truffles in a convenient CSV format, which restaurant chains will take over 
the FTP protocol.

Our internal development team has successfully implemented of this service and
our QA team made sure that what has been done is exactly what we wanted.

However since this service is incredibly important for our business, we 
decided to involve an independent expert to assess the technical quality of
the result. The solution will evolve in the future, so the quality of the 
code is of most importance.

**Your task will be:**

- _[mandatory]_ conduct a code review and describe the problems, if any;
- _[mandatory]_ describe the improvements if they are required;
- _[preferably]_ run the project using any convenient tool, like Laravel Sail;
- _[preferably]_ implement these improvements and make a pull request;
- _[optional]_ ensure the quality of the tests;


## Code review description

### Application issues

1. Common controller for all API methods.
2. FormRequest is not used for validation of incoming data.
3. Methods in controllers include additional logic which can be moved to services/repositories.
4. Checking user access in the controller method instead of using middleware.
5. ImportTruffle job and ProcessTruffle job include repeated logic, hardcoded file names, and work with filesystem and process cvs in one place without logical separation.
6. Using ftp instead sftp.
7. Using one ftp server for import and export.
8. All test cases in single file.
9. No logging and error handling
10. Not appropriate JSON data format for response
11. Only the first line is read from csv

### Suggestion for improvements (some suggestions are implemented)
1. Separate controllers for each logic (for instance, AuthController, TruffleController and so on)
2. Using FormRequest for validation of incoming data.
3. Adding type declarations for variables and methods
4. Adding errors handling and logging
5. Implementing JSOM format for API response
6. Removing unused directives and imports
7. Using sftp protocol instead ftp because sftp protocol is more secure. 
8. Using two different sftp connection for different purpose: one for manufacturer which will be import database with truffles and second one for restaurants which will be export processed file.
9. Adding middleware for groups that need to verify user authentication.
10. Refactoring ImportTruffle job and ProcessTruffle job classes - separation logic for each other, moving general logic to other classes like FileService for methods that should work with the file system and RedisRepository that should consist of methods related to Redis logic.
11. Using streams when working with the file system for efficient memory usage and increased performance.
12. Using transactions for jobs to ensure data integrity. For example, it is important for us that the data is stored in the database and written to the file in one iteration, and if something goes wrong, nothing will be executed.
13. Using chunks for processing large CSV files and instead of using fgetcsv can be used SplFileObject that provides the interface for working with files like objects it can be efficient when processing large files.
14. Using buffering for processing large CSV files to reduce the number of disk accesses.
15. Using parallel processing for large CSV files.
16. Using tools static analyze like PHPStan, PHPMD, PHP_CodeSniffer, PHP-CS-Fixer, Psalm. They will help to support code quality.

Notes: not all points were implemented.  
Notes: Tests are implemented partially