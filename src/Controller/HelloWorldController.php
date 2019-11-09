<?php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Query\PagerSelectExtender;


class HelloWorldController extends ControllerBase {
  // Implements hello funtion.
  public function hello(){
    return array(
      '#title'=>'Hello World!',
      '#markup'=>'Hello World!',
    );
 }


  public function content() {
    return [
      '#theme' => 'hello_world',
      '#test_var' => $this->t('Test Value'),
    ];
}

  // Implements display record in table.
  public function display() {
    //create table header
    $header_table = array(
      'id'=>    t('SrNo'),
      'image' => t('Upload'),
      'name' => t('Name'),
      'email' => t('Email'),
      'password' => t('Password'),
      'dob' => t('DOB'),
      'address' => t('Address'),
      'number' => t('Number'),
      'delete' => t('Delete'),
      'edit' => t('Edit'),
      );
    //select records from table
    $query = \Drupal::database()->select('hello_form', 'm');
    $query->fields('m', ['id','image','name','email','password','dob','address','number']);
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    $results = $pager->execute()->fetchAll();

    //$results = $query->execute()->fetchAll();
    $rows = [];
    foreach($results as $data) {
      $delete = Url::fromUserInput('/hello/delete/'.$data->id);
      $edit   = Url::fromUserInput('/hello-form?num='.$data->id);
      $rows[] = array(
        'id' =>$data->id,
        'image' => $data->image,
        'name' => $data->name,
        'email' => $data->email,
        'password' => $data->password,
        'dob' => $data->dob,
        'address' => $data->address,
        'number' => $data->number,
         \Drupal::l('Delete', $delete),
         \Drupal::l('Edit', $edit),
      );

    }
    //display data in site
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No users found'),
    ];

     //pager
     $form['pager'] = array(
      '#type' => 'pager'
    );
    return $form;
  }

  // Implements delete.

  function delete($id) {
    $result = \Drupal::database()->delete('hello_form')
      ->condition('id', $id)
      ->execute();
    return new RedirectResponse(\Drupal::url('hello_world.display_table_controller_display'));

  }
}

?>

