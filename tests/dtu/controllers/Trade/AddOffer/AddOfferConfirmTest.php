<?php
//TODO: write and correct the tests made by this dumb IA
namespace Trade\AddOffer;

use controllers\Trade\AddOffer\AddOfferConfirm;
use models\DataBase;
use PHPUnit\Framework\TestCase;

class AddOfferConfirmTest extends TestCase
{
  private AddOfferConfirm $addOfferConfirm;

  public function setUp(): void{
    // create a AddOfferConfirm instance
    $this->addOfferConfirm = new AddOfferConfirm();

    // create a mock for DataBase.php
/*    $mockDb = $this->getMockBuilder(DataBase::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['insertOffre'])
      ->getMock();

    $mockDb->expects($this->once())
      ->method('insertOffre')
      ->with(
        $this->equalTo('test@example.com'),
        $this->equalTo('Valid Offer'),
        $this->equalTo(100.0),
        $this->equalTo('Valid Description'),
        $this->equalTo('2023-12-31')
      );*/
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToFormIfFieldsAreEmpty() {
    $_POST = [
      'title' => null,
      'price' => null,
      'end_date' => null,
      'description' => null,
      'tag' => null
    ];

    // For some reason, php don't like when i use ob_end_clean().
    ob_start();
    $this->addOfferConfirm->control();
    $headers = xdebug_get_headers();

    $this->assertTrue(str_contains(ob_get_clean(), "Veuillez remplir tous les champs"));
    $this->assertContains('Location: /offre', $headers);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToFormIfPriceIsInvalid() {
    $_POST = [
      'title' => 'Test Offer',
      'price' => '-10',
      'end_date' => '2023-12-31',
      'description' => 'Test Description',
      'tag' => 'Test'
    ];

    //  the buffer to avoid output interference, caused by the two echo call in control()
    ob_start();
    $this->addOfferConfirm->control();
    ob_end_clean();

    $headers = xdebug_get_headers();
    $this->assertContains('Location: /offre', $headers);
  }


  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToMarketplaceOnValidInput() {
    $_SESSION['email'] = 'martin.demange@etu.univ-amu.fr';
    $_POST = [
      'title' => 'Valid Offer',
      'price' => '100',
      'end_date' => '2029-12-31',
      'description' => 'Valid Description',
      'tag' => 'Valid'
    ];


//    DataBase::setInstance($mockDb);

    ob_start();
    $this->addOfferConfirm->control();
    ob_end_clean();

    $headers = xdebug_get_headers();
    $this->assertContains('Location: /marketplace', $headers);
  }

  public function testResolvesCorrectPathAndMethod()
  {
    $this->assertTrue(AddOfferConfirm::resolve('/offre/confirm', 'POST'));
  }

  public function testDoesNotResolveIncorrectPath()
  {
    $this->assertFalse(AddOfferConfirm::resolve('/wrong-path', 'POST'));
  }

  public function testDoesNotResolveIncorrectMethod()
  {
    $this->assertFalse(AddOfferConfirm::resolve('/offre/confirm', 'GET'));
  }

}
