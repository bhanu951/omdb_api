# OMDB API

                                             .,.
                                            .cd:..
                                            .xXd,,'..
                                           .lXWx;;;,,..
                                         .,dXWXo;;;;;,,..
                                       .;dKWWKx:;;;;;;;,,'..
                                    .;oOXNXKOo;;;;;;;;;;;;,,,'..
                                 .:dOXWMMN0Okl;;;;;;;;;;;;;;;;,,,'..
                             .,lk0NMMMMMMNKOxc;;;;;;;;;;;;;;;;;;;;,,,'..
                         .'cx0XWMMMMMMMWX0kd:;;;;;;;;;;;;;;;;;;;;;;;;;,,,..
                      .'cx0NMMMMMMMMMWX0Oxl;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,'..
                    .;d0NMMMMMMMMMMWX0Oxl:;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,...
                  .:kXWMMMMMMMMMWNK0kdl:;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,'..
                .cONMMMMMMMMMWNX0Okoc;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,'.
              .;kNMMMMMMMMWNX0Okdl:;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,'..
             .oXMMMMMMWWXK0Oxdl:;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,'..
            ,oKWWWWNXKK0kxoc:;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,'..
           'lOO0000OOxdlc;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,'''.
          .,lxkxxdolc:;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,''''..
         .,;;;::;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,'''''..
        .,;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,'''''''.
       .';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,'''''''..
    .',;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,'''''''''..
    .,;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,''''''''''..
    ',;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,''''''''''''.
    ,,;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,''''''''''''''.
    ,;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,'''''''''''''''.
    ,;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;,,''''''''''''''''''
    ,;;;;;;;;;;;;;;;;;;;;;;;;;;;cldxkkOkkxxdlc;;;;;;;;;;;;;;;;;;;;;;;;,,''''''''''',''''''''
    ,;;;;;;;;;;;;;;;;;;;;;;;:ox0XWMMMMMMMMMWNX0kxdc;;;;;;;;;;;;;;;;,,,'''''''';cdk00Okl,''''
    ,;;;;;;;;;;;;;;;;;;;;;cxKWMMMMMMMMMMMMMMMMMMMWN0xl;;;;;;;;;;,,,'''''''';lkKWMMMMMMW0c'''
    ',;;;;;;;;;;;;;;;;;;:dKWMMMMMMMMMMMMMMMMMMMMMMMMMN0xl;;;;;,,'''''''';okXWMMMMMMMMMMM0:'.
    .,;;;;;;;;;;;;;;;;;:kNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMN0xl;''''''',:oOXWMMMMMMMMMMMMMMNd'.
    .',;;;;;;;;;;;;;;;;xWMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMN0xolccoxONMMMMMMMMMMMMMMMMMMWd'.
     .,;;;;;;;;;;;;;;;oXMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMWNWMMMMMMMMMMMMMMMMMMMMMMXl..
     .',;;;;;;;;;;;;;;xNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMWX0OOOKNMMMMMMMMMMMMMMMMMMMM0;.
      .',;;;;;;;;;;;;;dNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMWN0xl:,''',cxXWMMMMMMMMMMMMMMMMWd.
       .',;;;;;;;;;;;;lKMMMMMMMMMMMMMMMMMMMMMMMMMMMMMWKko:,'''''''''':dKWMMMMMMMMMMMMMWk,
        .',;;;;;;;;;;;;dXMMMMMMMMMMMMMMMMMMMMMMMMWX0xl;'''',;:::;,''''';oKWMMMMMMMMMMWO,
         ..',,;;;;;;;;;;o0NMMMMMMMMMMMMMMMMMMN0xdo:,''',cdO0XXXXK0kl,'''';o0WMMMMMMMNd,
           .''',,,,,,,,,,;lk0XWMMMMMMMMWNX0ko:,'''''';oONN0xdoodx0NNx,''''';lOXWWWXkc.
            ..'''''''''''''',:lloodddoolc;'''''''''',xN0dc,'''''',oK0:''''''',:clc;..
             ...''''''''''''''''''''''''''''''''''''':c,''''''''''';;''''''''''''..
               ..''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''...
                 ...'''''''''''''''''''''',lxdc,'''''''''''''''''',:oxkl''''''..
                    ...''''''''''''''''''':kNMNK0OxdollcccclllodxO0XX0d;'''..
                       ..'''''''''''''''''',cxOKXXNWMMWWWWWWWWNXKOxo:'''..
                          ...'.'''''''''''''''',;:clooodddoollc:,'''...
                               ....'''''''''''''''''''''''''''.....
                                   ....'..''''''''''''........

## Module Functionality :

This Module creates functionality to integrate Drupal and OMDB API.

This Module Creates a new Content Entity called as OMDB API.

This Module provides Movie details when search form is submitted in Drupal.
The detail are obtained by making an API call to the OMDB API end point.

This Module provides a page where all the OMDB API entities are listed.

This Module provides a block on OMDB API Entity pages where a QR code for
the particular page exists.

This Module provides custom breadcrumb for the OMDB API Entity pages.

This Module provides migration plugin to import data from CSV source.


## TODO :

1. Add workflow image can use https://bpmn.io/ for creating workflow image.
2. Add Bulk Operations Form.  --> Done
3. Update Entity List View.
4. Add Tests.
5. Add Devel Generate.
6. Add Devel Generate Drush Command.
7. Add Action Plugins.  --> Done

## Notes :

$dateTime = new DrupalDateTime();

    return t('@user - @date', [
      '@user' => \Drupal::currentUser()->getAccountName(),
      '@date' => \Drupal::service('date.formatter')
      ->format($dateTime->getTimestamp(), 'html_date'),
    ])->render();

https://dev.to/brunorobert/github-and-gitlab-sync-44mn


## Issues :

1. Might produce https://www.drupal.org/project/drupal/issues/3028354

The website encountered an unexpected error. Please try again later.
TypeError: Argument 2 passed to Drupal\content_translation\Controller\ContentTranslationController::add() must implement interface Drupal\Core\Language\LanguageInterface, null given in Drupal\content_translation\Controller\ContentTranslationController->add() (line 361 of core/modules/content_translation/src/Controller/ContentTranslationController.php).
