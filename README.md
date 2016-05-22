# Yet Another Tic Tac Toe Project

This is yet another implementation of Tic Tac Toe. This is a solution to a challenge presented by BroadbandTV and I have made it exclusively to the VanHackathon. 

Here are the features of this project:
* Fully functional RESTful API
* User login (with auto signup)
* Play mutiples games simultaneously
* Giveup on a game
* User's game history

The following items weren't included in this version of the project:
* Possibility to play Ultimate Tic Tac Toe
* Play online with another human being

# Technical information

This project was developed using PHP7 + MySQL + Symfony3.

# Installation

Execute the following steps to run this project:
Make sure your MySQL service is running with root user with no password.

    git clone https://github.com/regisdiogo/tic-tac-toe.git
    composer install
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    php bin/console server:run


# Game instructions

## First let's login

### Request
    POST /api/user/ HTTP/1.1
    Host: localhost:8000
    Content-Type: application/json
    {"email":"regisdiogof@gmail.com","password":"mysecretpass"}

> Internally we will look up the email as a strong key for our user. If none is found then we create a new user. If we actually found a user we validate the password.

### Response
    {"userHash":"574128282aaaa"}

> It will return the user's UUID hash. This value should be informed in every method to validate user's credentials.

## Now we can start a game

### Request
    POST /api/games/start/1/1 HTTP/1.1
    Host: localhost:8000
    Content-Type: application/json
    Authorization: 574128282aaaa

> The parameters 1/1 passed after 'start' parameter are currently default. In my inicial planning I was going to control Type (Regular or Ultimate Tic Tac Toe) and Mode (Versus COM, Versus Human Being or Versus Random Human Being). But now it's only accepting Type Regular and Mode Versus COM.

> The Authorization parameter passed on the header idenfify the user starting the game.

> This method will return a gameHash that has to be used to play the game.

### Response
    {"gameHash":"574216386bc64"}

## Playing the Game

### Request
    PUT /api/games/574216386bc64 HTTP/1.1
    Host: localhost:8000
    Authorization: 574128282aaaa
    Content-Type: application/json
    {"column":2,"row":2}

> The game hash is used in the URL to identify the game that is being played.

> The Authorization parameter passed on the header idenfify the user playing the game.

> On the body of the request we inform which column and row we would like to check. Default value for user is X. Must be a number between 1 and 3.

### Response
    {"gameplay":[["O","_","_"],["_","X","_"],["_","_","_"]]}

> In the response we see a gameplay chart of the actual board of Tic Tac Toe. That board already includes COM moves (default value O).

> When the game finishes a message is presented on Response.

## Giving Up

### Request
    PUT /api/games/574216386bc64/giveup HTTP/1.1
    Host: localhost:8000
    Authorization: 574128282aaaa
    Content-Type: application/json

> Adding the 'giveup' parameter to the end of a game URL will flag this game as abandoned. Every move is now denied.

### Response
    {"message":"Game finished without winners. :("}

## Retrieving user's game history

### Request
    GET /api/games/userhistory HTTP/1.1
    Host: localhost:8000
    Authorization: 574128282aaaa

### Response
    {"gameList":[{"gameHash":"57413b587e4ae","createdAt":"2016-05-22T01:53:44-0300","type":"Regular","mode":"Versus COM","finished":"true","abandoned":"false","gameplay":[["O","_","_"],["X","X","X"],["O","_","_"]],"winner":"PLAYER"},{"gameHash":"5741bc50567a9","createdAt":"2016-05-22T11:04:00-0300","type":"Regular","mode":"Versus COM","finished":"false","abandoned":"false","gameplay":[["_","_","_"],["_","_","_"],["_","_","_"]]},{"gameHash":"5741bc67ee51d","createdAt":"2016-05-22T11:04:23-0300","type":"Regular","mode":"Versus COM","finished":"true","abandoned":"true","gameplay":[["O","X","_"],["_","_","_"],["_","_","_"]]}]}
