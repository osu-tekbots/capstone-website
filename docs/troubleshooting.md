
# Current Migration onto official capstone site (eecs.oregonstate.edu/capstone/submission)
Process current is to pull and overwrite all changes using git 
1. Push all changes from STAGE directory (education/capstone/stage) to github
2. Navigate to official site location (capstone/submission)
3. Run - [ git fetch --all ]
4. Run - [ git reset --hard origin/master ]
5. From capstone/submission, run - [ sh scripts/allow.sh ] (Corrects all permission changes)

## Troubleshooting and Helpful Notes

### Problem
The `u_uap_provided_id` columns in the database are `VARCHAR(256)` and because Google Authentication returns an ID that 
is often times more than 64 bits, the session variable for userID can't be explicitly referenced in Javascript and will 
be truncated.
  
#### Solution 
Create a hidden div and echo out the SESSION variable there. Then reference that div in the javascript. Found in 
`pages/viewSingleProject.php`: 
		 
## Screenshots 

![image](https://user-images.githubusercontent.com/20714895/59056636-806d0b00-884d-11e9-8a94-606cb1e5f667.png)

![image](https://user-images.githubusercontent.com/20714895/59057000-43eddf00-884e-11e9-833a-ad1d8b329c7a.png)

![image](https://user-images.githubusercontent.com/20714895/59057030-55cf8200-884e-11e9-8937-fd465a732039.png)

![image](https://user-images.githubusercontent.com/20714895/59057421-2e2ce980-884f-11e9-83ad-6035f7787e94.png)

