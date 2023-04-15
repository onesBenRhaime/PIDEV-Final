<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCodeBundle\QrCodeFactoryInterface;

class QrcodeService
{
    /**
     * @var BuilderInterface
     */
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function qrcode( $user)
    {

        // set qrcode
        $result = $this->builder
            ->data("Your name : kacem.salma@esprit.tn"."Demande de Carte Bancaire"."Acceptée")
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->build()
        ;


        //Save img png
        $result->saveToFile('./QRcode/'.'client.png');

        return $result->getDataUri();
    }

    public function qrcodee( )
    {

        // set qrcode
        $result = $this->builder
            ->data("Your name : kacem.salma@esprit.tn "." Demande de Carte Bancaire :  "." est refusée")
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->build()
        ;


        //Save img png
        $result->saveToFile('./QRcode/'.'client2.png');
        

        return $result->getDataUri();
    }
}