

<?php echo $form['name']->renderLabel() ?> : <?php echo $contact->name . "\n"; ?> <br />
<?php echo $form['email']->renderLabel() ?> : <?php echo $contact->email . "\n"; ?> <br />
<?php echo $form['comments']->renderLabel() ?> : <?php echo nl2br($contact->comments) . "\n"; ?> <br />
 
