<?php
require_once __DIR__ . '/../vendor/autoload.php';

class PdfHelper extends FPDF
{
    private $title;
    private $logoPath;

    public function __construct($title = 'Document', $logoPath = null)
    {
        parent::__construct();
        $this->title = $title;
        $this->logoPath = $logoPath;
    }

    public function Header()
    {
        if ($this->logoPath && file_exists($this->logoPath)) {
            $this->Image($this->logoPath, 10, 6, 30);
        }
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, $this->title, 0, 0, 'C');
        $this->Ln(20);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    public function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $title, 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Ln(2);
    }

    public function InfoLine($label, $value)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 6, $label, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $value, 0);
        $this->Ln();
    }
    public function SimpleTable($headers, $data)
    {
        // En-têtes
        $this->SetFont('Arial', 'B', 10);
        foreach ($headers as $header) {
            $this->Cell(40, 7, $header, 1);
        }
        $this->Ln();
        
        // Données
        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            foreach ($row as $col) {
                $this->Cell(40, 6, $col, 1);
            }
            $this->Ln();
        }
    }
}