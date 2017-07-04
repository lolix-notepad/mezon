# Assets with CSS and JS files

##Intro##

This class allows you to compile HTML controls.

##Select controls##

First create a class wich extends base Asset class like this:

```PHP
// this method call compiles select controls
// wich allows you to select users from the array $Users with name 'select-name'
// field 'login' will be displayed in the list
// and all users will be identified by field 'id'
GUI::select_control( 'select-name' , $Users , 'id' , 'login' , 1 );
```