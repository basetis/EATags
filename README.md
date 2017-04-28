# EATags - Use your Evernote(r) as always, get more things done than ever.

## About
EATags is a service that binds with your Evernote account and allows you to invoke new features tagging your notes on your preferred Evernote client. Post tweets, post notes to wordpress, create a table of content from a note, create LaTeX images from formulas, and much more.

The service is developed with PHP framework [CodeIgniter 2.1.0](https://www.codeigniter.com/).

## Installation
1. Clone the repository on a LAMP environment
    * OAuth, headers, proxy and rewrite php/apache modules must be enabled
    * Make and enable the VirtualHost pointing to your cloned folder, and set the ENVIRONMENT in VirtualHost file.
    * The apache user must be the owner of the folder `application/logs` to properly write codeigniter logs
2. Create a mysql database
    1. Create a db user for the project and give him/her permissions to all the tables of the created schema
    2. Import the sql file with the structure and needed data
3. Request an Evernote (Full Access) API Key [Evernote Developer Documentation](http://dev.evernote.com/doc/)
    * For developing purposes you should use [Evernote's Sandbox](http://dev.evernote.com/doc/articles/testing.php)
    * Same for [google](https://console.developers.google.com), [twitter](https://apps.twitter.com/) and [flickr](https://www.flickr.com/services/api/misc.api_keys.html) if you want to enable those features
4. Register webhooks [Webhooks](https://dev.evernote.com/doc/articles/polling_notification.php#webhooks)
    * As they say on previous link, _open a developer support ticket_
    * Url must point to your url + /evernote_webhook/index
    * You can specify a *test_url* (your dev url + /evernote_webhook/eat_that) and *is_test_user* on Users table
5. In `application/config/constants.php` replace the *_KEYs and *_SECRETs of the mentioned services, the db info and *_URLs
    * Adapt `application/config/database.php` to your environments
6. Check everything works
    * web
    * login
    * configurations
    * use some eat.tag on Evernote, verify you recieve the webhook and the system process the tag (you should see entries on log)

## How it works
1. Evernote user registers on EATags, confirms email and account is activated.
2. User links Evernote account with EATags.
3. User tags some note with an eat.tag
4. Evernote sends a webhook (GET notification) to YOUR_URL + /evernote_webhook/index that looks like:
    * [base URL]/?userId=[user ID]&guid=[note GUID]&notebookGuid=[notebook GUID]&reason=update
5. If the user is a **test_user** index() method redirects to **test_url** (mentioned at installation's 4th point)
    * This way you can have just a webhook reciever and multiple developer's working environments
6. eat_that() method in Evernote_webhook Controller recieves the webhook, makes validations and sends the tag to Action_note library
7. execute_action_by_tag() method in Action_note sends the tag to execute_action() method of the tag's model
8. If there's no error, model returns the new note, and Evernote_webhook sends the update to Evernote.
9. User should see the changes on his/her note (if in web, a refresh may be needed)
    * where the eat.tag has been removed and an eaten:[tag] (defined at tags db table) inserted

## First steps with CodeIgniter
* Read their [user guide](https://www.codeigniter.com/user_guide/)

* File's structure:

    * eat/application/views => layout (we slightly use bootstrap 2.0.4 & {less})
    * eat/application/controllers => code that directly connects with views and models
    * eat/application/models => db stuff and business logic
    * eat/application/libraries => loaded external libraries