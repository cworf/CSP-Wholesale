<?php
/**
 * WooCommerce Jetpack PDF Invoice
 *
 * The WooCommerce Jetpack PDF Invoice class.
 *
 * @class    WCJ_PDF_Invoice
 * @version  2.1.3
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_PDF_Invoice' ) ) :

class WCJ_PDF_Invoice extends WCJ_Invoice {

    /**
     * Constructor.
     */
    public function __construct( $order_id, $invoice_type ) {
		parent::__construct( $order_id, $invoice_type );
    }
	
	function prepare_pdf() {
	
		$invoice_type = $this->invoice_type;
	
		// Include the main TCPDF library
		require_once( WCJ()->plugin_path() . '/includes/lib/tcpdf_min/tcpdf.php' );

		// Create new PDF document
		$pdf = new TCPDF( 
			get_option( 'wcj_invoicing_' . $invoice_type . '_page_orientation', 'P' ), 
			PDF_UNIT, 
			//PDF_PAGE_FORMAT, 
			get_option( 'wcj_invoicing_' . $invoice_type . '_page_format', 'A4' ), 
			true, 
			'UTF-8', 
			false 
		);

		// Set document information
		$pdf->SetCreator( PDF_CREATOR );
		//$pdf->SetAuthor( 'Algoritmika Ltd.' );
		$pdf->SetTitle( 'Invoice' );
		$pdf->SetSubject( 'Invoice PDF' );
		$pdf->SetKeywords( 'invoice, PDF' );

		// Header - set default header data
		if ( 'yes' === get_option( 'wcj_invoicing_' . $invoice_type . '_header_enabled' ) ) {
			$the_logo = '';
			$the_logo_width_mm = 0;		
			if ( '' != get_option( 'wcj_invoicing_' . $invoice_type . '_header_image' ) ) {
				$the_logo = parse_url( get_option( 'wcj_invoicing_' . $invoice_type . '_header_image' ) );
				$the_logo = $the_logo['path'];
				$the_logo_width_mm = get_option( 'wcj_invoicing_' . $invoice_type . '_header_image_width_mm' );
			}
			$pdf->SetHeaderData(
				$the_logo,
				$the_logo_width_mm,
				do_shortcode( get_option( 'wcj_invoicing_' . $invoice_type . '_header_title_text' ) ),
				do_shortcode( get_option( 'wcj_invoicing_' . $invoice_type . '_header_text' ) . apply_filters( 'wcj_get_option_filter', ' - ' . 'powered by ' . 'woojetpack' . '.com', '' ) ),
				wcj_hex2rgb( get_option( 'wcj_invoicing_' . $invoice_type . '_header_text_color' ) ),
				wcj_hex2rgb( get_option( 'wcj_invoicing_' . $invoice_type . '_header_line_color' ) ) );
		} else {
			$pdf->SetPrintHeader( false );
		}

		// Footer
		if ( 'yes' === get_option( 'wcj_invoicing_' . $invoice_type . '_footer_enabled' ) ) {
			$pdf->setFooterData(
				wcj_hex2rgb( get_option( 'wcj_invoicing_' . $invoice_type . '_footer_text_color' ) ),
				wcj_hex2rgb( get_option( 'wcj_invoicing_' . $invoice_type . '_footer_line_color' ) ) );
		} else {	
			$pdf->SetPrintFooter( false );
		}

		// Set Header and Footer fonts
		$pdf->setHeaderFont( Array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
		$pdf->setFooterFont( Array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );

		// Set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// Set margins
		$pdf->SetMargins( 
			get_option( 'wcj_invoicing_' . $invoice_type . '_margin_left' ), 
			get_option( 'wcj_invoicing_' . $invoice_type . '_margin_top' ), 
			get_option( 'wcj_invoicing_' . $invoice_type . '_margin_right' ) );
		$pdf->SetHeaderMargin( get_option( 'wcj_invoicing_' . $invoice_type . '_margin_header' ) );
		$pdf->SetFooterMargin( get_option( 'wcj_invoicing_' . $invoice_type . '_margin_footer' ) );

		// Set auto page breaks
		$pdf->SetAutoPageBreak( TRUE, get_option( 'wcj_invoicing_' . $invoice_type . '_margin_bottom' ) );

		// Set image scale factor
		$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

		/*// Set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}*/

		// Set default font subsetting mode
		$pdf->setFontSubsetting( true );

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to print standard ASCII chars, you can use core fonts like  helvetica or times to reduce file size.
		$pdf->SetFont(
			apply_filters( 'wcj_get_option_filter', 'dejavusans', get_option( 'wcj_invoicing_' . $invoice_type . '_general_font_family', 'dejavusans' ) ),
			'',
			apply_filters( 'wcj_get_option_filter', 8, get_option( 'wcj_invoicing_' . $invoice_type . '_general_font_size', 8 ) ),
			'',
			true );

		// Add a page
		$pdf->AddPage();

		// Set text shadow effect
		if ( 'yes' === get_option( 'wcj_invoicing_' . $invoice_type . '_general_font_shadowed', 'yes' ) ) {			
			$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
		}

		return $pdf;		
	}

    /**
     * get_pdf.
     */
    //function get_pdf( $get_by_order_id = 0 ) {
	//function get_pdf( $order_id, $invoice_type, $name, $dest ) {
	function get_pdf( $dest ) {

		$pdf = $this->prepare_pdf();
		
		// Get invoice content HTML
		$_GET['order_id'] = $this->order_id;
		$html = do_shortcode( get_option( 'wcj_invoicing_' . $this->invoice_type . '_template' ) );
		//$html = apply_filters( 'the_content', get_option( 'wcj_invoicing_' . $this->invoice_type . '_template' ) );
		
		$styling = '<style>' . get_option( 'wcj_invoicing_' . $this->invoice_type . '_css' ) . '</style>';
		
		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell( 0, 0, '', '', $styling . $html, 0, 1, 0, true, '', true );		
		
				
		/*		
		// set style for barcode
		$style = array(
			'border' => true,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);		
				
		// -------------------------------------------------------------------
		// PDF417 (ISO/IEC 15438:2006)

		/*

		 The $type parameter can be simple 'PDF417' or 'PDF417' followed by a
		 number of comma-separated options:

		 'PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6'

		 Possible options are:

			 a  = aspect ratio (width/height);
			 e  = error correction level (0-8);

			 Macro Control Block options:

			 t  = total number of macro segments;
			 s  = macro segment index (0-99998);
			 f  = file ID;
			 o0 = File Name (text);
			 o1 = Segment Count (numeric);
			 o2 = Time Stamp (numeric);
			 o3 = Sender (text);
			 o4 = Addressee (text);
			 o5 = File Size (numeric);
			 o6 = Checksum (numeric).

		 Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional.
		 To use a comma character ',' on text options, replace it with the character 255: "\xff".

		*//*

		$pdf->write2DBarcode( 'www.woojetpack.com', 'PDF417', 0, 200, 0, 30, $style, 'T');
		//$pdf->Text(80, 85, 'PDF417 (ISO/IEC 15438:2006)');

		// -------------------------------------------------------------------		
		/**
		require_once( wcj_plugin_path() .'/includes/lib/tcpdf_min/tcpdf_barcodes_2d.php');		
		$barcodeobj = new TCPDF2DBarcode('http://www.tcpdf.org', 'PDF417');
		// output the barcode as PNG image
		//$barcodeobj->getBarcodePNG(4, 4, array(0,0,0));		
		$html = $barcodeobj->getBarcodeHTML(4, 4, 'black');
		//$pdf->writeHTMLCell( 0, 0, '', '', $html, 0, 1, 0, true, '', true );		
		/**/

		// Close and output PDF document		
		$result_pdf = $pdf->Output( '', 'S' );
		$file_name = $this->get_file_name();
		$file_path = sys_get_temp_dir() . '/' . $file_name;
		if ( ! file_put_contents( $file_path, $result_pdf ) )
			return null;
		
		if ( 'F' === $dest ) {
			return $file_path;
		}
		elseif ( 'D' === $dest || 'I' === $dest ) {
			if ( 'D' === $dest ) {
				header( "Content-Type: application/octet-stream" );
				header( "Content-Disposition: attachment; filename=" . urlencode( $file_name ) );
				header( "Content-Type: application/octet-stream" );
				header( "Content-Type: application/download" );
				header( "Content-Description: File Transfer" );
			}
			elseif ( 'I' === $dest ) {
				header( "Content-type: application/pdf" );
				header( "Content-Disposition: inline; filename=" . urlencode( $file_name ) );
			}	
			header( "Content-Length: " . filesize( $file_path ) );
			flush(); // this doesn't really matter.
		
			$fp = fopen($file_path, "r");
			while (!feof($fp))
			{
				echo fread($fp, 65536);
				flush(); // this is essential for large downloads
			}
			fclose($fp);			
		}
		return null;
	}
}

endif;
