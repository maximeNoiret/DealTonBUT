<?php

use PHPUnit\Framework\TestCase;
use views\Trade\TradeSubjectPoint\TradeSubjectPointView;
use models\DataBase;

class TradeSubjectPointTest extends TestCase {

    /** @test */
    public function returns_correct_template_path() {
        $view = new TradeSubjectPointView();
        $expectedPath = __DIR__ . DIRECTORY_SEPARATOR . 'TradeSubjectPointTemplate.html';
        $this->assertEquals($expectedPath, $view->path());
    }

    /** @test */
    public function builds_from_and_to_options_based_on_user_subjects_and_points() {
        $email = 'user@example.com';

        $dbMock = $this->getMockBuilder(DataBase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSubject', 'getPoints'])
            ->getMock();

        $dbMock->expects($this->once())
            ->method('getSubject')
            ->with($email)
            ->willReturn([
                ['subject_name' => 'R Math'],
                ['subject_name' => 'R Physics']
            ]);

        $dbMock->expects($this->exactly(2))
            ->method('getPoints')
            ->withConsecutive([$email, 'Math'], [$email, 'Physics'])
            ->willReturnOnConsecutiveCalls(10, 20);

        $this->mockStaticMethod(DataBase::class, 'getInstance', $dbMock);

        $_SESSION['email'] = $email;
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $view = new TradeSubjectPointView();
        $values = $view->templateValues();

        $this->assertArrayHasKey('FROM_OPTIONS', $values);
        $this->assertArrayHasKey('TO_OPTIONS', $values);

        $this->assertStringContainsString('Math (10 pts)', $values['FROM_OPTIONS']);
        $this->assertStringContainsString('Physics (20 pts)', $values['FROM_OPTIONS']);
        $this->assertStringContainsString('Math (10 pts)', $values['TO_OPTIONS']);
        $this->assertStringContainsString('Physics (20 pts)', $values['TO_OPTIONS']);
    }

    /** @test */
    public function points_transfer_success_sets_success_flash_and_calls_transfer() {
        $email = 'user@example.com';

        $dbMock = $this->getMockBuilder(DataBase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPoints', 'transferPoints', 'getSubject'])
            ->getMock();

        $dbMock->expects($this->any())
            ->method('getSubject')
            ->with($email)
            ->willReturn([['subject_name' => 'R FromSub'], ['subject_name' => 'R ToSub']]);

        $dbMock->expects($this->once())
            ->method('getPoints')
            ->with($email, 'FromSub')
            ->willReturn(50.0);

        $dbMock->expects($this->once())
            ->method('transferPoints')
            ->with($email, 20.0, 'FromSub', 'ToSub');

        $this->mockStaticMethod(DataBase::class, 'getInstance', $dbMock);

        $_SESSION['email'] = $email;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'form_type' => 'points_transfer',
            'from_subject' => 'FromSub',
            'to_subject' => 'ToSub',
            'points' => '20'
        ];

        $view = new TradeSubjectPointView();
        $values = $view->templateValues();

        $this->assertStringContainsString('Transfert réussi', $values['FLASH']);
    }

    /** @test */
    public function points_transfer_with_insufficient_points_sets_error_flash() {
        $email = 'user@example.com';

        $dbMock = $this->getMockBuilder(DataBase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPoints', 'getSubject', 'transferPoints'])
            ->getMock();

        $dbMock->expects($this->any())
            ->method('getSubject')
            ->with($email)
            ->willReturn([['subject_name' => 'R FromSub'], ['subject_name' => 'R ToSub']]);

        $dbMock->expects($this->once())
            ->method('getPoints')
            ->with($email, 'FromSub')
            ->willReturn(5.0);

        $dbMock->expects($this->never())
            ->method('transferPoints');

        $this->mockStaticMethod(DataBase::class, 'getInstance', $dbMock);

        $_SESSION['email'] = $email;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'form_type' => 'points_transfer',
            'from_subject' => 'FromSub',
            'to_subject' => 'ToSub',
            'points' => '10'
        ];

        $view = new TradeSubjectPointView();
        $values = $view->templateValues();

        $this->assertStringContainsString('Tu n\\\'as pas assez de points', $values['FLASH']);
    }

    /** @test */
    public function points_transfer_with_same_subject_sets_error_flash() {
        $email = 'user@example.com';

        $dbMock = $this->getMockBuilder(DataBase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSubject'])
            ->getMock();

        $dbMock->expects($this->any())
            ->method('getSubject')
            ->with($email)
            ->willReturn([['subject_name' => 'R SameSub']]);

        $this->mockStaticMethod(DataBase::class, 'getInstance', $dbMock);

        $_SESSION['email'] = $email;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'form_type' => 'points_transfer',
            'from_subject' => 'SameSub',
            'to_subject' => 'SameSub',
            'points' => '5'
        ];

        $view = new TradeSubjectPointView();
        $values = $view->templateValues();

        $this->assertStringContainsString('Tu dois choisir deux matières différentes', $values['FLASH']);
    }

    /** @test */
    public function ics_import_success_inserts_subjects_and_sets_success_flash() {
        $email = 'user@example.com';

        $dbMock = $this->getMockBuilder(DataBase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['insertSubjectSafe', 'getSubject'])
            ->getMock();

        $dbMock->expects($this->any())
            ->method('getSubject')
            ->with($email)
            ->willReturn([]);

        $dbMock->expects($this->atLeastOnce())
            ->method('insertSubjectSafe');

        $this->mockStaticMethod(DataBase::class, 'getInstance', $dbMock);

        $_SESSION['email'] = $email;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['form_type' => 'ics_import'];

        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);
        $tmpFilename = $meta['uri'];
        fwrite($tmp, "BEGIN:VCALENDAR\nSUMMARY:SR101\nSUMMARY:Math\nEND:VCALENDAR");
        fflush($tmp);

        $_FILES = [
            'ics_file' => [
                'tmp_name' => $tmpFilename,
                'error' => 0
            ]
        ];

        $view = new TradeSubjectPointView();
        $values = $view->templateValues();

        fclose($tmp);

        $this->assertStringContainsString('Matières importées avec succès', $values['FLASH']);
    }

    /** @test */
    public function ics_import_with_upload_error_sets_error_flash() {
        $email = 'user@example.com';

        $dbMock = $this->getMockBuilder(DataBase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSubject'])
            ->getMock();

        $dbMock->expects($this->any())
            ->method('getSubject')
            ->with($email)
            ->willReturn([]);

        $this->mockStaticMethod(DataBase::class, 'getInstance', $dbMock);

        $_SESSION['email'] = $email;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['form_type' => 'ics_import'];

        $_FILES = [
            'ics_file' => [
                'tmp_name' => '',
                'error' => 1
            ]
        ];

        $view = new TradeSubjectPointView();
        $values = $view->templateValues();

        $this->assertStringContainsString('Erreur lors de l\\\'upload du fichier', $values['FLASH']);
    }

    private function mockStaticMethod($class, $method, $returnValue) {
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->onlyMethods([$method])
            ->getMock();

        $mock->expects($this->once())
            ->method($method)
            ->willReturn($returnValue);

        return $mock;
    }

    protected function tearDown(): void {
        $_POST = [];
        $_FILES = [];
        $_SESSION = [];
        $_SERVER = [];
    }
}
