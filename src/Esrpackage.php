<?php

/* ------------------------------------------------------------------------ 
 * Esrpackage.php
 * Eine Klasse um Einzahlungsscheine mit ESR-Nummer als PDF zu erstellen.
 * A class to create Swiss payment slips with ESR number in pdf format.
 * See https://github.com/checkitsedo/esrtest
 * Ergänzungen von checkit senti.lu
 * Additions from checkit senti.lu
 * ------------------------------------------------------------------------ 


/**************************************
 * Import FPDF-Class
 * You can get the latest version here:
 * http://www.fpdf.de/downloads/releases/
 * or here: http://www.fpdf.org/
 *
 * Adjust path if necessary.
 **************************************/
 
namespace Checkitsedo\Esrpackage;

require_once('../vendor/fpdf/fpdf.php');

class Esrpackage
{
	//Ränder in mm
	private $marginTop = 0;
	private $marginLeft = 0;

	//Werte auf dem Einzahlungsschein
	private $ezs_bankName = '';
	private $ezs_bankCity = '';
	private $ezs_bankingAccount = '';
	
	private $ezs_recipientName    = '';
	private $ezs_recipientAddress = '';
	private $ezs_recipientCity    = '';
	private $ezs_bankingCustomerIdentification = '';
	
	private $ezs_payerLine1		  = '';
	private $ezs_payerLine2       = '';
	private $ezs_payerLine3       = '';
	private $ezs_payerLine4       = '';
	private $ezs_payerFullAddress = false;
	
	private $ezs_referenceNumber = '';
	private $ezs_amount = 0;
	private $paymentReason = '';
	
	private $pdf = false;
	private $landscapeOrPortrait = "P";
	private $format = "A4";
	
	private $pathToImage = '';
	private $type =  'orange';


	/**
	 * Constructor method for this class
	 */
	public function __construct($marginTop=0, $marginLeft=0, $pdfObject=false, $landscapeOrPortrait="P", $format="A4"){
		
		//Sachen einstellen
		$this->marginTop = $marginTop;
		$this->marginLeft = $marginLeft;
		$this->landscapeOrPortrait = $landscapeOrPortrait;
		$this->format = $format;
		
		if($pdfObject != false){
			$this->pdf = $pdfObject;
		}//if
	}
	
	/**
	 * Verarbeiten der Eingaben aus dem Formular
	 */
	public function esr($bankName, $bankCity, $bankingAccount, $recipientName, $recipientAddress, $recipientCity, $bankingCustomerIdentification, $payerLine1, $payerLine2, $payerLine3, $payerLine4, $amount, $referenceNumber) {
		
		//Create a new pdf to create your invoice, already using FPDF
		//(if you don't understand this part you should have a look at the FPDF documentation)
		$pdf = new FPDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetAutoPageBreak(0,0);
		$pdf->SetFont('Helvetica','B',5.975);
		$pdf->SetTextColor(205,81,56);
		$pdf->SetXY(0, 0);
		$pdf->Cell(50, 377, utf8_decode("!<Sie können Ihre unten stehenden Daten kopieren und in Ihr e-Banking/e-Finance-Zahlungsformular einfügen. Der folgende Abschnitt kann nicht als Einzahlungsschein am Postschalter verwendet werden.>!"));
		$pdf->SetTextColor(0,0,0);
		
		//Fügen Sie jetzt einfach Ihren Einzahlungsschein hinzu. Senden Sie Ihre PDF-Instanz an die Klasse Einzahlungsschein
		$ezs = new Einzahlungsschein(191, 0, $pdf);
		
		$bankName = $_POST["bankName"];
		$bankCity = $_POST["bankCity"];
		$bankingAccount = $_POST["bankingAccount"];
		
		$recipientName = $_POST["recipientName"];
		$recipientAddress = $_POST["recipientAddress"];
		$recipientCity = $_POST["recipientCity"];
		$bankingCustomerIdentification = $_POST["bankingCustomerIdentification"];
		
		$payerLine1 = $_POST["payerLine1"];
		$payerLine2 = $_POST["payerLine2"];
		$payerLine3 = $_POST["payerLine3"];
		$payerLine4 = $_POST["payerLine4"];
		
		$referenceNumber = $_POST["referenceNumber"];
		$amount = $_POST["amount"];
		$paymentReason = $_POST["paymentReason"];
		
		$ezs->setBankData($bankName, $bankCity, $bankingAccount);
		$ezs->setRecipientData($recipientName, $recipientAddress, $recipientCity, $bankingCustomerIdentification);
		$ezs->setPayerData($payerLine1, $payerLine2, $payerLine3, $payerLine4);
		$ezs->setPaymentData($amount, $referenceNumber);
		$ezs->createEinzahlungsschein(false, true);
		
		$pdf->output();
	}
	
	//Typ setzen
	public function setType($type){
		$this->type = $type;
	}
	
	/**
	 * Set name, address and banking account of bank
	 * @param string $bankName
	 * @param string $bankCity
	 * @param string $bankingAccount
	 * @return bool
	 */
	public function setBankData($bankName, $bankCity, $bankingAccount){
		$this->ezs_bankName = utf8_decode($bankName);
		$this->ezs_bankCity = utf8_decode($bankCity);
		$this->ezs_bankingAccount = $bankingAccount;
		return true;
	}
	
	/**
	 * Set name and address of recipient of money (= you, I guess)
	 * @param string $recipientName
	 * @param string $recipientAddress
	 * @param string $recipientCity
	 * @param int    $bankingCustomerIdentification
	 * @return bool
	 */
	public function setRecipientData($recipientName, $recipientAddress, $recipientCity, $bankingCustomerIdentification){
		$this->ezs_recipientName    = $recipientName;
		$this->ezs_recipientAddress = $recipientAddress;
		$this->ezs_recipientCity    = $recipientCity;
		$this->ezs_bankingCustomerIdentification = $bankingCustomerIdentification;
		return true;
	}
	
	/**
	 * Set name and address of payer (very flexible four lines of text)
	 * @param string $payerLine1
	 * @param string $payerLine2
	 * @param string $payerLine3
	 * @param string $payerLine4
	 * @return bool
	 */
	public function setPayerData($payerLine1, $payerLine2, $payerLine3='', $payerLine4=''){
		$this->ezs_payerLine1 = $payerLine1;
		$this->ezs_payerLine2 = $payerLine2;
		$this->ezs_payerLine3 = $payerLine3;
		$this->ezs_payerLine4 = $payerLine4;
		return true;
	}
	
	/**
	 * Set name and address of payer (very flexible four lines of text)
	 * @param string $payerLine1
	 * @param string $payerLine2
	 * @param string $payerLine3
	 * @param string $payerLine4
	 * @return bool
	 */
	public function setPayerFullAddress($address){
		$this->ezs_payerFullAddress = $address;
		return true;
	}
	
	/**
	 * Set payment data
	 * @param float $amount
	 * @param int   $referenceNumber (
	 * @return bool
	 */
	public function setPaymentData($amount, $referenceNumber=null){
		$this->ezs_amount 		   = sprintf("%01.2f",$amount);
		$this->ezs_referenceNumber = $referenceNumber;
		return true;
	}
	
	/**
	 * Set payment reason
	 * @param string $txt
	 * @return bool
	 */
	public function setPaymentReason($txt){
		$this->paymentReason   = utf8_decode($txt);
		return true;
	}
	
	/**
	 * Does the magic!
	 * @param bool $doOutput
	 * @param string $filename
	 * @param string $saveAction (I, D, F, or S -> see http://www.fpdf.de/funktionsreferenz/?funktion=Output)
	 * @return string or file
	 */
	public function createEinzahlungsschein($doOutput=true, $displayImage=false, $fileName='', $saveAction=''){
		
		//Set basic stuff
		if(!$this->pdf){
			$this->pdf = new FPDF($this->landscapeOrPortrait,'mm',$this->format);
			$this->pdf->AddPage();
			$this->pdf->SetAutoPageBreak(0);
		}//if
		
		//Place image
		if($displayImage){
			$this->pdf->Image($this->pathToImage."ezs_".$this->type.".png", $this->marginLeft, $this->marginTop, 210, 106, "PNG");
		}//if
		
		//Set font
		$this->pdf->SetFont('Helvetica','',9);
		
		//Place name of bank (twice)
		$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+9);
		$this->pdf->Cell(50, 4,$this->ezs_bankName);
		$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+13);
		$this->pdf->Cell(50, 4,$this->ezs_bankCity);
		
		$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+9);
		$this->pdf->Cell(50, 4,$this->ezs_bankName);
		$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+13);
		$this->pdf->Cell(50, 4,$this->ezs_bankCity);
		
		//Place banking account (twice)
		$this->pdf->SetXY($this->marginLeft+27, $this->marginTop+42.75);
		$this->pdf->Cell(30, 4,$this->ezs_bankingAccount);
		
		$this->pdf->SetXY($this->marginLeft+87.5, $this->marginTop+42.75);
		$this->pdf->Cell(30, 4,$this->ezs_bankingAccount);
		
		//Place money amount (twice)
		if($this->ezs_amount > 0){
			$amountParts = explode(".", $this->ezs_amount);
		} else
		{
			$amountParts[0] = "--";
			$amountParts[1] = "--";
		}//if
		
		$offset = 50.5;
		if($this->type == 'red'){$offset = 51.5;};
		
		$this->pdf->SetFont('Helvetica','',12);
		
		$this->pdf->SetXY($this->marginLeft+7, $this->marginTop+$offset+0.7);
		$this->pdf->Cell(35, 4,$amountParts[0], 0, 0, "R");
		$this->pdf->SetXY($this->marginLeft+48.5, $this->marginTop+$offset+0.7);
		$this->pdf->Cell(6, 4,$amountParts[1], 0, 0, "C");
		
		$this->pdf->SetXY($this->marginLeft+67.6, $this->marginTop+$offset+0.7);
		$this->pdf->Cell(35, 4,$amountParts[0], 0, 0, "R");
		$this->pdf->SetXY($this->marginLeft+109.2, $this->marginTop+$offset+0.7);
		$this->pdf->Cell(6, 4,$amountParts[1], 0, 0, "C");
		
		//Place name of receiver (twice)
		if($this->type == 'red'){
			$this->pdf->SetFont('Helvetica','',9);
			
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+23);
			$this->pdf->Cell(50, 4,$this->formatIban(utf8_decode($this->ezs_bankingCustomerIdentification)));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+27);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientName));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+31);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientAddress));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+35);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientCity));
			
			$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+23);
			$this->pdf->Cell(50, 4,$this->formatIban(utf8_decode($this->ezs_bankingCustomerIdentification)));
			$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+27);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientName));
			$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+31);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientAddress));
			$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+35);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientCity));
		} else
		{
			$this->pdf->SetFont('Helvetica','',9);
			
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+21.5);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientName));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+25.5);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientAddress));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+29.5);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientCity));
			
			$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+21.5);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientName));
			$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+25.5);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientAddress));
			$this->pdf->SetXY($this->marginLeft+62.6, $this->marginTop+29.5);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_recipientCity));
		}
		
		//Place name of Payer (twice)
		if($this->ezs_payerFullAddress){
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+64);
			$this->pdf->MultiCell(50, 4, utf8_decode($this->ezs_payerFullAddress), 0, 'L');
			
			$this->pdf->SetXY($this->marginLeft+123, $this->marginTop+48);
			$this->pdf->MultiCell(50, 4, utf8_decode($this->ezs_payerFullAddress), 0, 'L');
		} else
		{
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+66);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine1));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+70);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine2));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+74);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine3));
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+78);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine4));
			
			$this->pdf->SetXY($this->marginLeft+123.2, $this->marginTop+47);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine1));
			$this->pdf->SetXY($this->marginLeft+123.2, $this->marginTop+51);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine2));
			$this->pdf->SetXY($this->marginLeft+123.2, $this->marginTop+55);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine3));
			$this->pdf->SetXY($this->marginLeft+123.2, $this->marginTop+59);
			$this->pdf->Cell(50, 4,utf8_decode($this->ezs_payerLine4));
		}
		
		//Reference number
		if($this->type == 'orange'){
			//create complete reference number
			$completeReferenceNumber = $this->createCompleteReferenceNumber();
			
			//Place Reference Number (twice)
			$this->pdf->SetFont('Helvetica','',10);
			$this->pdf->SetXY($this->marginLeft+125, $this->marginTop+34.25);
			$this->pdf->Cell(80, 4, $this->breakStringIntoBlocks($completeReferenceNumber));
			
			$this->pdf->SetFont('Helvetica','',7);
			$this->pdf->SetXY($this->marginLeft+2, $this->marginTop+60);
			$this->pdf->Cell(50, 4, $this->breakStringIntoBlocks($completeReferenceNumber));
		}//if
		
		//Payment reason
		if($this->type == 'red'){
			$this->pdf->SetXY($this->marginLeft+125, $this->marginTop+12);
			$this->pdf->MultiCell(50, 4, $this->paymentReason, 0, 'L');
		}//if
		
		//Bottom line
		if($this->type == 'orange'){
			//create bottom line string
			$bottomLineString = '';
			
			//EZS with amount or not?
			if($this->ezs_amount == 0){
				$bottomLineString .= "042>";
			} else
			{
				$amountParts = explode(".", $this->ezs_amount);
				$bottomLineString .= "01";
				$bottomLineString .= str_pad($amountParts[0], 8 ,'0', STR_PAD_LEFT);
				$bottomLineString .= str_pad($amountParts[1], 2 ,'0', STR_PAD_RIGHT);
				$bottomLineString .= $this->modulo10($bottomLineString);
				$bottomLineString .= ">";
			}//if
			
			//add reference number
			$bottomLineString .= $this->createCompleteReferenceNumber();
			$bottomLineString .= "+ ";
			
			//add banking account
			$bankingAccountParts = explode("-", $this->ezs_bankingAccount);
			$bottomLineString .= str_pad($bankingAccountParts[0], 2 ,'0', STR_PAD_LEFT);
			$bottomLineString .= str_pad($bankingAccountParts[1], 6 ,'0', STR_PAD_LEFT);
			$bottomLineString .= str_pad($bankingAccountParts[2], 1 ,'0', STR_PAD_LEFT);
			$bottomLineString .= ">";
			
			//Set bottom line
			$this->pdf->AddFont('OCRB10');
			$this->pdf->SetFont('OCRB10','',10);
			$this->pdf->SetXY($this->marginLeft+63, $this->marginTop+85);
			$this->pdf->Cell(140,4,$bottomLineString, 0, 0, "R");
		} else
		{
			//set font
			$this->pdf->AddFont('OCRB10');
			$this->pdf->SetFont('OCRB10','',10);
			
			//add banking account
			$bottomLineString = '';
			$bottomLineString .= str_pad(substr($this->ezs_bankingCustomerIdentification, 8), 26 ,'0', STR_PAD_LEFT);
			$bottomLineString .= $this->modulo10($bottomLineString);
			$bottomLineString .= "+";
			
			$bottomLineString2 = '';
			$bcNummer = substr($this->ezs_bankingCustomerIdentification, 4, 5);
			$bottomLineString2 .= " 07".$bcNummer;
			$bottomLineString2 .= $this->modulo10($bcNummer);
			$bottomLineString2 .= $this->modulo10($bottomLineString2);
			$bottomLineString2 .= ">";
			$bottomLineString  .= $bottomLineString2;
			
			//Set bottom line
			$this->pdf->SetXY($this->marginLeft+67, $this->marginTop+85);
			$this->pdf->Cell(140,4,$bottomLineString, 0, 0, "R");
			
			//add banking account
			$bottomLineString = '';
			$bankingAccountParts = explode("-", $this->ezs_bankingAccount);
			$bottomLineString .= str_pad($bankingAccountParts[0], 2 ,'0', STR_PAD_LEFT);
			$bottomLineString .= str_pad($bankingAccountParts[1], 6 ,'0', STR_PAD_LEFT);
			$bottomLineString .= str_pad($bankingAccountParts[2], 1 ,'0', STR_PAD_LEFT);
			$bottomLineString .= ">";
			
			//Set bottom line
			$this->pdf->SetXY($this->marginLeft+67, $this->marginTop+92);
			$this->pdf->Cell(140,4,$bottomLineString, 0, 0, "R");
		}//if
		
		//Output
		if($doOutput){
			$this->pdf->Output($fileName, $saveAction);
			if($fileName != ''){
				return $fileName;
			}//if
		}//if
	}
	
	/**
	 * Creates Modulo10 recursive check digit
	 *
	 * as found on http://www.developers-guide.net/forums/5431,modulo10-rekursiv
	 * (thanks, dude!)
	 *
	 * @param string $number
	 * @return int
	 */
	private function modulo10($number) {
		$table = array(0,9,4,6,8,2,7,1,3,5);
		$next = 0;
		for ($i=0; $i<strlen($number); $i++) {
			$next = $table[($next + substr($number, $i, 1)) % 10];
		}//for
		return (10 - $next) % 10;
	}
	
	/**
	 * Creates complete reference number
	 * @return string
	 */
	private function createCompleteReferenceNumber() {
		//get reference number and fill with zeros
		$completeReferenceNumber = str_pad($this->ezs_referenceNumber, (26 - strlen($this->ezs_bankingCustomerIdentification)) ,'0', STR_PAD_LEFT);
		
		//add customer identification code
		$completeReferenceNumber = $this->ezs_bankingCustomerIdentification.$completeReferenceNumber;
		
		//add check digit
		$completeReferenceNumber .= $this->modulo10($completeReferenceNumber);
		
		//return
		return $completeReferenceNumber;
	}
	
	/**
	 * Displays a string in blocks of a certain size.
	 * Example: 00000000000000000000 becomes more readable 00000 00000 00000
	 * @param string $string
	 * @param int $blocksize
	 * @return string
	 */
	private function breakStringIntoBlocks($string, $blocksize=5, $alignFromRight=true) {
		//lets reverse the string (because we want the block to be aligned from the right)
		if($alignFromRight){
			$string = strrev($string);
		}//if
		
		//chop it into blocks
		$string = trim(chunk_split($string, $blocksize, ' '));
		
		//re-reverse
		if($alignFromRight){
			$string = strrev($string);
		}//if
		
		return $string;
	}
	
	/**
	 * Formats IBAN number in human readable format
	 * @return string
	 */
	private function formatIban($iban){
		return $this->breakStringIntoBlocks($iban, 4, false);
	}
}
