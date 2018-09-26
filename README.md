# SlashTrace - Awesome error handler - Bugsnag handler

This is the [Bugsnag](https://www.bugsnag.com/) handler for [SlashTrace](https://github.com/slashtrace/slashtrace). 
Use it to send your errors and exceptions to your Sentry account.

## Usage

1. Install using Composer:

   ```
   composer require slashtrace/slashtrace-bugsnag
   ```
   
2. Hook it into SlashTrace:

   ```PHP
   use SlashTrace\SlashTrace;
   use SlashTrace\Bugsnag\BugsnagHandler;

   $handler = new BugsnagHandler("Your Bugsnag API key");
    
   $slashtrace = new SlashTrace();
   $slashtrace->addHandler($handler);
   ```
   
   Alternatively, you can pass in a pre-configured Bugsnag client when you instantiate the handler:
   
   ```
   $bugsnag = Bugsnag\Client::make('Your Bugsnag API key');
   $handler = new BugsnagHandler($bugsnag);
   
   $slashtrace->addHandler($handler);
   ```      
   
Read the [SlashTrace](https://github.com/slashtrace/slashtrace) docs to see how to capture errors and exceptions, and how to attach additional data to your events.
