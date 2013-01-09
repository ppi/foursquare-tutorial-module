#Mashing up FourSquare and Google Maps with GeoLocation and APC#

## Module Name: FourSquareModule ##
================

Sample code for our article 'Mashing Up Foursquare with Google Maps and APC' 
http://www.ppi.io/blog/1/foursquare-with-apc

In this article, we cover how to work with the framework as a whole by making a module, including a controller, routes, templates (views) and services by writing a real-world application. 

In order to achieve this we are going to use the foursquare API and then APC for caching the API lookups. With the venues we pull from foursquare we plot these venues on a Google Maps display.

The purpose of this module is to place foursquare venues on a google map based on your current geo-location as a user.