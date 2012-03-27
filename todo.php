<?php
   /*
    * @todo Got current date, now just need to insert that when contract is filled. Check
    *       if semester already exists before inserting.
    *
    * @todo Make new contract form appear with already filled out information
    *       at specific time intervals (e.g every new semester). When the time
    *       comes for the form to appear again, set the user's "agreedto_contract"
    *       field in "coop_persons" table to 0;
    * 
    * @todo Set access to the contract new view so only people who need to fill 
    *       it out can visit it. Others (such as non normal users or users who 
    *       have filled it out already) get some message saying they don't need
    *       to fill it out.
    * 
    * @todo When a user fills out the contract, add to db if not already, and 
    *       make sure they get updated session information (maybe by clearing
    *       their auth->identity and redirecting them to auth/cas).
    * 
    * @todo When user contract expires, must figure out how to get update contract
    *       form rather than the new contract form. Maybe check if user had already
    *       submitted a contract by checking if their primary key is in the join
    *       table for persons and contracts. If it is, then they get redirected
    *       to contract/update rather than contract/new.
    */
   
?>
