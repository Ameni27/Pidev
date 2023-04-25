<?php
namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class MailService{

    public function __construct(private \Symfony\Component\Mailer\Transport\TransportInterface $transport) {
        $this->transport = $transport;
    }


    public function sendEmail(
        $to = '',
        $template=null,
        $subject = '',
        $context=null
        
    ): void {
        
       
            #->attachFromPath('data:application/pdf;base64,'. base64_encode($pdfContent), 'facture.pdf');


        
        
        $email = (new TemplatedEmail())
            ->from('nidhal.barek@esprit.tn')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);
            
            $mailer = new \Symfony\Component\Mailer\Mailer($this->transport);
            $mailer->send($email);
    }




}

?>