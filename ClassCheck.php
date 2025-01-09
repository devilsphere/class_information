<?php
	
	// Include FPDF
	require_once __DIR__ . '/fpdf/fpdf.php';
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_name'])) {
		$className = trim($_POST['class_name']);
		
		if (class_exists($className)) {
			try {
				$reflection = new ReflectionClass($className);
				
				ob_start(); // Start capturing HTML output
				
				// Generate HTML content for display
				echo "<h1>{$className} Class Methods</h1>";
				echo "<p>This document provides a comprehensive list of methods and their details for the {$className} class.</p>";
				echo "<hr>";
				
				$methodsData = []; // Collect method data for the PDF
				foreach ($reflection->getMethods() as $method) {
					$methodData = [
						'name' => $method->getName(),
						'visibility' => $method->isPublic() ? 'Public' : ($method->isProtected() ? 'Protected' : 'Private'),
						'static' => $method->isStatic() ? 'Yes' : 'No',
						'returnType' => $method->hasReturnType() ? $method->getReturnType() : 'None',
					];
					$methodsData[] = $methodData;
					
					echo "<h3>Method: {$methodData['name']}</h3>";
					echo "<p><strong>Visibility:</strong> {$methodData['visibility']}</p>";
					echo "<p><strong>Static:</strong> {$methodData['static']}</p>";
					echo "<p><strong>Return Type:</strong> {$methodData['returnType']}</p>";
					echo "<hr>";
				}
				
				$htmlContent = ob_get_clean(); // Get the generated HTML content
				
				// Save HTML to a file
				$htmlFileName = "{$className}_Class_Methods.html";
				file_put_contents($htmlFileName, $htmlContent);
				
				// Generate PDF using FPDF
				$pdf = new FPDF();
				$pdf->AddPage();
				$pdf->SetFont('Arial', 'B', 16);
				$pdf->Cell(0, 10, "{$className} Class Methods", 0, 1, 'C');
				$pdf->SetFont('Arial', '', 12);
				$pdf->Ln(5);
				
				foreach ($methodsData as $methodData) {
					$pdf->Cell(0, 10, "Method: {$methodData['name']}", 0, 1);
					$pdf->Cell(0, 10, "Visibility: {$methodData['visibility']}", 0, 1);
					$pdf->Cell(0, 10, "Static: {$methodData['static']}", 0, 1);
					$pdf->Cell(0, 10, "Return Type: {$methodData['returnType']}", 0, 1);
					$pdf->Ln(5);
				}
				
				$pdfFileName = "{$className}_Class_Methods.pdf";
				$pdf->Output('F', $pdfFileName); // Save PDF to a file
				
				// Display output and download links
				echo "<div>";
				echo "<p><a href='{$htmlFileName}' download>Download as HTML</a></p>";
				echo "<p><a href='{$pdfFileName}' download>Download as PDF</a></p>";
				echo "</div>";
				
				echo $htmlContent;
				
			} catch (ReflectionException $e) {
				echo "<p>Error: Unable to reflect on class '{$className}'. Details: " . htmlspecialchars($e->getMessage()) . "</p>";
			}
		} else {
			echo "<p>Error: Class '{$className}' does not exist.</p>";
		}
	} else {
		?>
        <form method="POST" action="">
            <h1>Class Inspector</h1>
            <p>Enter the name of the class to inspect:</p>
            <input type="text" name="class_name" placeholder="Enter class name" required>
            <button type="submit">Check Class</button>
        </form>
		<?php
	}
?>
