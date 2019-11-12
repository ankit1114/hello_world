<?php

namespace Drupal\hello_world\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Render\Element;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class HelloWorldForm extends FormBase
{

  public function getFormId()
  {
    //get form id
    return 'hello-form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    //DB connection
    $conn = Database::getConnection();
    $record = array();
    if (isset($_GET['num'])) {
      $query = $conn->select('hello_form', 'm')
        ->condition('id', $_GET['num'])
        ->fields('m');
      $record = $query->execute()->fetchAssoc();
    }

    $form['#attributes']['enctype'] = 'multipart/form-data';
    $form['image'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload your file'),
      //'#required' => true,
      '#upload_location' => 'public://images/',
      '#default_value' => (isset($record['image']) && $_GET['num']) ? $record['image']:'',
    );

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Candidate Name:'),
      //'#required' => TRUE,
      '#default_value' => (isset($record['name']) && $_GET['num']) ? $record['name']:'',
    );

    $form['email'] = array(
     '#type' => 'email',
     '#title' => t('Candidate Email:'),
     //'#required' => TRUE,
     '#default_value' => (isset($record['email']) && $_GET['num']) ? $record['email']:'',
    );

    $form['password'] = array(
      '#type' => 'password',
      '#title' => t('Password:'),
      //'#required' => TRUE,
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
      '#type' => 'tel',
      '#title' => t('Contact number:'),
      '#required' => TRUE,
      '#default_value' => (isset($record['number']) && $_GET['num']) ? $record['number']:'',
    );

    $form['gender'] = array(
      '#type' => 'select',
      '#title' => $this
        ->t('Select Gender'),
      '#options' => [
        '' => $this
          ->t('Select'),
        'male' => $this
          ->t('Male'),
        'female' => $this
          ->t('Female'),
         'person' => $this
          ->t('Person'),
        ],
      '#default_value' => (isset($record['gender']) && $_GET['num']) ? $record['gender']:'',
    );

    $form['copy'] = array(
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Send me a copy'),
    );

    /*$form['submit'] = [
      '#type' => 'submit',
      '#value' => 'save',
      //'#value' => t('Submit'),
      //'#ajax' => [
      //'callback' => '::setMessage',
    ];*/
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('save'),
      '#ajax' => [
        'callback' => '::setMessage',
        ],
      ];

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>',
    ];

    return $form;
  }

  public function setMessage(array $form, FormStateInterface $form_state) {

    $response = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '.result_message',
        '<div class="my_message">Submitted Successfully  ' . $form_state->getValue('name') . '</div>')
  );
    return $response;
    }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*$field = $form_state->getValues();
    $name = isset($field['name']) ? $field['name'] : '';
    $email = isset($field['email']) ? $field['email'] : '';
    $number = isset($field['number']) ? $field['number'] : '';

    if (strlen($name) < 5) {
      $form_state->setErrorByName('name', $this->t('Name is too short.'));
    }*/

  /*if(preg_match("/^\d+\.?\d*$/",$number) && strlen($number)==10){
  }
  else
  {
    $form_state->setErrorByName('number', $this->t('Mobile number is too short.'));
  }
*/

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    \Drupal::messenger()->addMessage($this->t('Form Submitted Successfully'), 'status', TRUE);

    $message = [
      '#theme' => 'status_messages',
      '#message_list' => drupal_get_messages(),
    ];

    $messages = \Drupal::service('renderer')->render($message);
    echo $messages;
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#result-message', $messages));
    return $response;

    $field = $form_state->getValues();
    $image = !empty($field['image']) ? $field['image'] : '';
    $name = isset($field['name']) ? $field['name'] : '';
    $email = isset($field['email']) ? $field['email'] : '';
    $password = isset($field['password']) ? $field['password'] : '';
    $dob = isset($field['dob']) ? $field['dob'] : '';
    $address = isset($field['address']) ? $field['address'] : '';
    $number = isset($field['number']) ? $field['number'] : '';
    $gender = isset($field['gender']) ? $field['gender'] : '';

    $field  = array(
      'image'   => $image,
      'name'   => $name,
      'email'   => $email,
      'password'   => $password,
      'dob'   => $dob,
      'address'   => $address,
      'number'   => $number,
      'gender'  => $gender,
    );

    // DB connection.
    $conn = Database::getConnection();
    if (isset($_GET['num'])){
      $conn->update('hello_form')
        ->fields($field)
        ->condition('id', $_GET['num'])
        ->execute();
      drupal_set_message("succesfully updated");
      $form_state->setRedirect('hello_world.display_table_controller_display');
    }
    else
    {
      $conn->insert('hello_form')
        ->fields($field)
        ->execute();
      drupal_set_message("succesfully saved");
      $response = new RedirectResponse(\Drupal::url('hello_world.display_table_controller_display'));
      $response->send();
   }

  }
}
