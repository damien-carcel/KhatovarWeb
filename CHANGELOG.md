#1.0.0 (2016-xx-xx)
##Technical improvements
- Migrate on Symfony 3 and Documents 0.4
- Form fields that were added through EventSubscriber in the form types or in the controllers are now added in form subscribers
- Activation of the default homepage and contact page are now manage through an "activation handler" called from the controller

##BC breaks
- PhotoHelper::getPhotoEntities return an array as "label => code" instead of "code => label"
- All class parameters has been removed
- Controllers are no longer services
- All FormType are now in a namespace "Khatovar\Bundle\XxxBundle\Form\Type"
- Activation of the default homepage and contact page has been extracted from the controllers

#0.9.5 (2015-11-08)
##Bug-fix
- Correct a bug that prevents photos to be displayed on member pages
- Update to Documents 0.3.1

#0.9.4 (2015-10-11)
##New feature
- Foundation tooltips for every form fields
- Tip messages are using the translation module

#0.9.3 (2015-10-04)
##Enhancement
- Photo substitution name is automatically set at upload accordingly to file name
- Enhance exaction date display

#0.9.2 (2015-10-03)
##Bug-fixes
- Correct a bug that prevents to attach a photo already uploaded to an exaction
- Correct a bug when no photo is attached to an exaction that is not in photo only mode

##Enhancement
- Display complete name for appearances in photo form and index

#0.9.1 (2015-09-27)
##Bug-fixes
- Adding a missing doctrine parameter
- Correct a bug that prevents to create a new contact page
- Correct a bug that prevents to create and activate a camp description at the same time
- Display the khatovar logo for past exactions if no photo have been uploaded
- Correct a bug that prevent a photo album to appear

##Enhancements
- Add an intro page to the Appearance bundle
- Display past exactions in decreasing order
- New exaction are "photo only" by default
- Map is not required to create a new exaction
- Better date picker in Exaction form

#0.9 (2015-09-26)
- First complete release, making the entire web site editable by its members
