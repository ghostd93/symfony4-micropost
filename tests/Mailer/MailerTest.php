<?php

namespace App\Tests\Mailer;

use App\Entity\User;
use Twig\Environment as Twig;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{
    public function testConfirmationEmail()
    {
        $user = new User();
        $user->setEmail('test@email.com');

        $swiftMailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $swiftMailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function($subject){
                $messageStr = (string) $subject;
                return strpos($messageStr, 'From: me@domain.com') !== false
                    && strpos($messageStr, 'Content-Type: text/html; charset=utf-8') !== false
                    && strpos($messageStr, 'To: test@email.com') !== false
                    && strpos($messageStr, 'Subject: Welcome to the Micro Post App') !== false
                    && strpos($messageStr, 'This is a message body') !== false;
                }));

        $twig = $this->getMockBuilder(Twig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twig->expects($this->once())
            ->method('render')
            ->with('email/registration.html.twig', [
                'user' => $user
            ])
            ->willReturn('This is a message body');

        $mailer = new Mailer($swiftMailer, $twig, 'me@domain.com');
        $mailer->sendConfirmationEmail($user);
    }
}
