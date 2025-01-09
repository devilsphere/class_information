<?php
	
	require_once __DIR__ . '/fpdf/fpdf.php'; // Include FPDF
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_name'])) {
		$className = trim($_POST['class_name']);
		
		if (class_exists($className)) {
			try {
				$reflection = new ReflectionClass($className);
				
				ob_start(); // Start capturing HTML output
				
				// Generate HTML content
				echo "<h1>{$className} Class Methods</h1>";
				echo "<p>This document provides a comprehensive list of methods and their details for the {$className} class.</p><hr>";
				
				$methodsData = []; // Store method details for the PDF
				
				foreach ($reflection->getMethods() as $method) {
					$methodData = [
						'name' => $method->getName(),
						'visibility' => $method->isPublic() ? 'Public' : ($method->isProtected() ? 'Protected' : 'Private'),
						'static' => $method->isStatic() ? 'Yes' : 'No',
						'abstract' => $method->isAbstract() ? 'Yes' : 'No',
						'final' => $method->isFinal() ? 'Yes' : 'No',
						'declaringClass' => $method->getDeclaringClass()->getName(),
						'returnType' => $method->hasReturnType() ? $method->getReturnType() : 'None',
						'parameters' => [],
						'docComment' => $method->getDocComment() ? htmlspecialchars($method->getDocComment()) : 'None'
					];
					
					echo "<h3>Method: {$methodData['name']}</h3>";
					
					// Add visibility, static, abstract, and final details
					echo "<p><strong>Visibility:</strong> {$methodData['visibility']}</p>";
					echo "<p><strong>Static:</strong> {$methodData['static']}</p>";
					echo "<p><strong>Abstract:</strong> {$methodData['abstract']}</p>";
					echo "<p><strong>Final:</strong> {$methodData['final']}</p>";
					echo "<p><strong>Declared in:</strong> {$methodData['declaringClass']}</p>";
					echo "<p><strong>Return Type:</strong> {$methodData['returnType']}</p>";
					
					// Parameters
					echo "<p><strong>Parameters:</strong></p>";
					if ($method->getParameters()) {
						echo "<ul>";
						foreach ($method->getParameters() as $parameter) {
							$paramDetails = $parameter->getName();
							if ($parameter->hasType()) $paramDetails .= " (Type: {$parameter->getType()})";
							if ($parameter->isOptional()) $paramDetails .= " [Optional]";
							if ($parameter->isDefaultValueAvailable()) $paramDetails .= " [Default: " . var_export($parameter->getDefaultValue(), true) . "]";
							if ($parameter->isPassedByReference()) $paramDetails .= " [By Reference]";
							if ($parameter->isVariadic()) $paramDetails .= " [Variadic]";
							$methodData['parameters'][] = $paramDetails;
							echo "<li>{$paramDetails}</li>";
						}
						echo "</ul>";
					} else {
						echo "<p>None</p>";
					}
					
					// Doc Comment
					echo "<p><strong>Doc Comment:</strong></p><pre>{$methodData['docComment']}</pre>";
					
					echo "<hr>";
					
					$methodsData[] = $methodData; // Add method data for PDF generation
				}
				
				$htmlContent = ob_get_clean(); // Get the generated HTML content
				
				// Save HTML to file
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
					$pdf->SetFont('Arial', 'B', 12);
					$pdf->Cell(0, 10, "Method: {$methodData['name']}", 0, 1);
					$pdf->SetFont('Arial', '', 12);
					$pdf->Cell(0, 10, "Visibility: {$methodData['visibility']}", 0, 1);
					$pdf->Cell(0, 10, "Static: {$methodData['static']}", 0, 1);
					$pdf->Cell(0, 10, "Abstract: {$methodData['abstract']}", 0, 1);
					$pdf->Cell(0, 10, "Final: {$methodData['final']}", 0, 1);
					$pdf->Cell(0, 10, "Declared in: {$methodData['declaringClass']}", 0, 1);
					$pdf->Cell(0, 10, "Return Type: {$methodData['returnType']}", 0, 1);
					$pdf->MultiCell(0, 10, "Parameters: " . (!empty($methodData['parameters']) ? implode(', ', $methodData['parameters']) : 'None'));
					$pdf->Ln(5);
					$pdf->MultiCell(0, 10, "Doc Comment: {$methodData['docComment']}");
					$pdf->Ln(10);
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
