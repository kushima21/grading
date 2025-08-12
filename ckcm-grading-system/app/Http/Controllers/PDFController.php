<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use TCPDF;

class CustomPDF extends TCPDF
{
    public function Header()
    {
        // Leave empty to remove the default header (including the black line)
    }
}

class PDFController extends Controller
{
    public function generatePDF(Request $request)
    {
        $studentID = $request->query('studentID');

        // Fetch user and grades
        $user = User::where('studentID', $studentID)->with('grades')->first();

        if (!$user || !$user->grades || $user->grades->isEmpty()) {
            return redirect()->back()->with('error', 'No grades found for this student.');
        }

        // Create PDF instance
        $pdf = new CustomPDF('P', 'mm', array(215.9, 330.2), true, 'UTF-8', false);
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CKCM Grading System');
        $pdf->SetTitle('Student Grades');
        $pdf->SetSubject('User Grades');
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 10);
        // Header (School Information)
        $pdf->Image(public_path('system_images/logo.jpg'), 35, 11, 25); // Adjust logo placement
        $pdf->SetFont('times', 'B', 11); // 'B' makes it bold
        $pdf->Cell(0, 5, "CHRIST THE KING COLLEGE DE MARANDING, INC.", 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 9); // Reset back to normal weight after bold text
        $pdf->Cell(0, 5, "Maranding, Lala, Lanao del Norte", 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 3, "Contact#: Administrator's Office (063)388-7039, Finance Office", 0, 1, 'C');
        $pdf->Cell(0, 3, "(063)388-7282, Registrar Tel. Fax #:(063)388-7373", 0, 1, 'C');
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 3, "9211 PHILIPPINES", 0, 1, 'C');
        $pdf->SetFont('times', 'BI', 10.2);
        $pdf->Cell(0, 10, "OFFICE OF THE COLLEGE REGISTRAR", 0, 1, 'C');

        $pdf->Ln(-2); // Moves the cursor UP by 3 units, reducing space

        $pdf->SetLineWidth(0.5);
        $pdf->Line(30, $pdf->GetY(), 180, $pdf->GetY()); // Draw line closer to the text

        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(0, 5, "EVALUATION COPY", 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Ln(0);

        // Student Info
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(120, 5, "NAME: " . strtoupper($user->name), 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(50, 5, "Sex: ". $user->gender, 0, 0, 'L');  // Adjust gender if available
        $pdf->Cell(50, 5, "ID No: " . $user->studentID, 0, 1, 'L');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, 1, "Course: Bachelor of Science in Computer Science", 0, 1, 'L'); // Adjust course dynamically if needed
        $pdf->Ln(0);

        $pdf->Ln(-1);
        $pdf->SetFont('helvetica', 'B',9);
        $pdf->Cell(0, 5, "COLLEGAITE RECORD", 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Ln(-1);

        // Group grades by academic year and period
        $groupedGrades = $user->grades->groupBy(['academic_year', 'academic_period']);

        // Iterate through grouped grades
        foreach ($groupedGrades as $academicYear => $periods) {
            foreach ($periods as $academicPeriod => $grades) {
                // Display Academic Year and Period as Section Headers
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->Cell(0, 6, "Academic Year: $academicYear - $academicPeriod", 0, 1, 'L');

                // Set thinner border
                $pdf->SetLineWidth(0.2); // Reduce from default (0.5) to 0.2 for thinner borders


                // Table Header
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->SetFillColor(200, 200, 200);
                $pdf->Cell(40, 7, "Subject Code", 1, 0, 'C', true); // Changed from "No." to "Subject Code"
                $pdf->Cell(94, 7, "Descriptive Titles", 1, 0, 'C', true);
                $pdf->Cell(20, 7, "Final Grade", 1, 0, 'C', true);
                $pdf->Cell(20, 7, "Re-Exam", 1, 0, 'C', true);
                $pdf->Cell(20, 7, "Credit", 1, 1, 'C', true);

                // Table Body
                $pdf->SetFont('helvetica', '', 8);
                foreach ($grades as $grade) {
                    $pdf->Cell(40, 7, $grade->subject_code, 1); // Use subject code instead of number
                    $pdf->Cell(94, 7, $grade->descriptive_title, 1);
                    $pdf->Cell(20, 7, number_format($grade->final, 2), 1, 0, 'C');
                    $pdf->Cell(20, 7, "", 1, 0, 'C'); // Re-exam column (empty)
                    $pdf->Cell(20, 7, $grade->units, 1, 1, 'C');
                }

                $pdf->Ln(2);

            }
        }

        $pdf->Cell(200, 5, "**Nothing Follows**", 0, 0, 'C'); // Left align
        $pdf->Ln(10);

        // Footer
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(120, 5, "NOT VALID WITHOUT THE COLLEGE SEAL", 0, 0, 'L'); // Left align

        // Right-aligned Name
        $pdf->Cell(70, 5, "ELVIN P. SALMERON, MM-EM", 0, 1, 'R'); // Name

        // Move cursor slightly up to bring the underline closer
        $pdf->Ln(-3.5); // Adjust the negative value to move the underline closer
        // Underline (Placed below the name)
        $pdf->Cell(120, 5, "", 0, 0); // Empty space to align underline
        $pdf->Cell(70, 2, "__________________________", 0, 1, 'R'); // Right align underline

        // Right-aligned Title
        $pdf->Cell(120, 5, "", 0, 0); // Empty space to align title
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(70, 5, "Registrar", 0, 1, 'R'); // Title


        // Output PDF
        $pdf->Output('grades.pdf', 'I');
    }
}
