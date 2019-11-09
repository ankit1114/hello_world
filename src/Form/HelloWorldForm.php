<?php

namespace Drupal\hello_world\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Render\Element;

class HelloWorldForm extends FormBase
{

  public function getFormId()
  {
    return 'hello-form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $conn = Database::getConnection();

     $record = array();
    if (isset($_GET['num'])) {
        $query = $conn->select('hello_form', 'm')
            ->condition('id', $_GET['num'])
            ->fields('m');
        $record = $query->execute()->fetchAssoc();
    }
     //$form['#attributes']['enctype'] = 'multipart/form-data';
     $form['image'] = array(
      '#type' => 'file',
      '#title' => t('Upload your file'),
      //'#required' => true,
     //'#upload_location' => 'public://images/'
      '#default_value' => (isset($record['image']) && $_GET['num']) ? $record['image']:'',
    );
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Candidate Name:'),
      '#required' => TRUE,
      '#default_value' => (isset($record['name']) && $_GET['num']) ? $record['name']:'',
      );
    $form['email'] = array(
     '#type' => 'email',
     '#title' => t('Candidate Email:'),
     '#required' => TRUE,
     '#default_value' => (isset($record['email']) && $_GET['num']) ? $record['email']:'',
     );
    $form['password'] = array(
      '#type' => 'password',
      '#title' => t('Password:'),
      '#required' => TRUE,
       '#default_value' => (isset($record['password']) && $_GET['num']) ? $record['password']:'',
      );
     $form['dob'] = array (
      '#type' => 'textfield',
      '#title' => t('AGE:'),
      '#default_value' => (isset($record['dob']) && $_GET['num']) ? $record['dob']:'',
    );
     $form['address'] = array(
      '#type' => 'textfield',
      '#title' => t('Address:'),
       '#default_value' => (isset($record['address']) && $_GET['num']) ? $record['address']:'',
      );
     $form['number'] = array(
      '#type' => 'number',
      '#title' => t('Contact number:'),
       '#default_value' => (isset($record['number']) && $_GET['num']) ? $record['number']:'',
      );

     $form['submit'] = [
        '#type' => 'submit',
        '#value' => 'save',
        //'#value' => t('Submit'),
    ];
    return $form;
  }

  public function hello_world_validate($form, &$form_state) {
  // YOUR CUSTOM VALIDATION CODE GOES HERE
    //$value = $form_state->('email');
    //if($value == !\Drupal::service('email.validator')->isValid($value))){
     //$form_state->setErrorByname('email',t('The email address %mail is not valid.',array('%mail'=>$value)));


   if (!valid_email_address($email)) {
   form_set_error('submitted][email_address', t('The email address appears to be invalid.'));
   }
 }

  public function submitForm(array &$form, FormStateInterface $form_state) {



$field=$form_state->getValues();
   $image=$field['image'];
   $name=$field['name'];
   $email=$field['email'];
   $password=$field['password'];
   $dob=$field['dob'];
   $address=$field['address'];
   $number=$field['number'];
print_r($field);
die("test1");

  if (isset($_GET['num'])) {

          $field  = array(
              'image'   => $image,
              'name'   => $name,
              'email'   => $email,
              'password'   => $password,
              'dob'   => $dob,
              'address'   => $address,
              'number'   => $number,
              );
          $conn = Database::getConnection();
          $conn->update('hello_form')
              ->fields($field)
              ->condition('id', $_GET['num'])
              ->execute();
          drupal_set_message("succesfully updated");

    $form_state->setRedirect('hello_world.display_table_controller_display');
      }
       else
       {
           $conn = Database::getConnection();
           $conn->insert('hello_form')->fields(
           array(
            'image' => $form_state->getValue('image'),
            'name' => $form_state->getValue('name'),
            'email' => $form_state->getValue('email'),
            'password' => $form_state->getValue('password'),
            'dob' => $form_state->getValue('dob'),
            'address' => $form_state->getValue('address'),
            'number' => $form_state->getValue('number'),
            )
          )
          ->execute();
          drupal_set_message("succesfully saved");


           $response = new RedirectResponse(\Drupal::url('hello_world.display_table_controller_display'));
           $response->send();

   }
  }

}


?>
