<?php


            namespace App\Http\Controllers;


            use Illuminate\Http\Request;
            use App\Models\SignedContract;

            use PDF;


            class PDFController extends Controller
            {
                public function generateHtmlToPDF(Request $request, $id)
                {
                    $signedContract = SignedContract::where('id', 11)
                        ->with('campaigns')
                        ->with('person')->first();
                    $html = $signedContract->campaign->contractText;

                    //Add Personal Information
                    $html .= $signedContract->person->fullname . '</br>';
                    $html .= 'DNI: ' . $signedContract->person->dni . '</br>';
                    $html .= '<img width="200" height="100" src="data:image/png;base64,';
                    $html .= base64_encode(
                            file_get_contents(
                                public_path('storage/'.$signedContract->signature)
                            )
                            );
                    $html .= '">';
                    $pdf= PDF::loadHTML($html);
                    return $pdf->download('contrato.pdf');
                }
            }