## Session Variables
Session variables are used to persist user data throughout the course of a user's active session. The instantiation 
of these variables occur in the following workflow:
  
1. The user visits the `pages/login.php` page. 
2. The user selects a login authentication type (EX: Google, Microsoft).
3. After successful authentication, the following session variables are instantiated and can be used in PHP throughout the entire application: 
   - `$_SESSION['userID']`: This variable is a string of numbers. 
   - `$_SESSION['accessLevel']`: This variable is a string that can be either: 
      - "Student"
      - "Proposer"
      - "Admin"
   - `$_SESSION['newUser']`: This variable is a boolean (either true or false).

> **NOTE**: Please do NOT reference `$_SESSION['userID']` in javascript, as Google Authentication may provide a 
> userID that is longer than the acceptable max character length for javascript. Instead, echo the session varible in a 
> hidden div and reference that text of that div in order to use the userID in JavaScript.

