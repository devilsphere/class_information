<?php
	require 'vendor/autoload.php'; // Include Dompdf autoload if using Composer
	
	use Dompdf\Dompdf;
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_name'])) {
		$className = trim($_POST['class_name']);
		
		if (class_exists($className)) {
			try {
				$reflection = new ReflectionClass($className);
				
				// Start capturing output to prepare it for saving or display
				ob_start();
				
				// Generate HTML content
				echo "<h1>{$className} Class Methods</h1>";
				echo "<p>This document provides a comprehensive list of methods and their details for the {$className} class.</p>";
				echo "<hr>";
				
				foreach ($reflection->getMethods() as $method) {
					echo "<h3>Method: {$method->getName()}</h3>";
					
					// Visibility
					if ($method->isPublic()) echo "<p><strong>Visibility:</strong> Public</p>";
					if ($method->isProtected()) echo "<p><strong>Visibility:</strong> Protected</p>";
					if ($method->isPrivate()) echo "<p><strong>Visibility:</strong> Private</p>";
					
					// Static, Abstract, Final
					if ($method->isStatic()) echo "<p><strong>Static:</strong> Yes</p>";
					if ($method->isAbstract()) echo "<p><strong>Abstract:</strong> Yes</p>";
					if ($method->isFinal()) echo "<p><strong>Final:</strong> Yes</p>";
					
					// Declaring Class
					echo "<p><strong>Declared in:</strong> {$method->getDeclaringClass()->getName()}</p>";
					
					// Return Type
					echo "<p><strong>Return Type:</strong> " . ($method->hasReturnType() ? $method->getReturnType() : 'None') . "</p>";
					
					// Parameters
					echo "<p><strong>Parameters:</strong></p>";
					if ($method->getParameters()) {
						echo "<ul>";
						foreach ($method->getParameters() as $parameter) {
							$details = $parameter->getName();
							if ($parameter->hasType()) $details .= " (Type: {$parameter->getType()})";
							if ($parameter->isOptional()) $details .= " [Optional]";
							if ($parameter->isDefaultValueAvailable()) $details .= " [Default: " . var_export($parameter->getDefaultValue(), true) . "]";
							if ($parameter->isPassedByReference()) $details .= " [By Reference]";
							if ($parameter->isVariadic()) $details .= " [Variadic]";
							echo "<li>{$details}</li>";
						}
						echo "</ul>";
					} else {
						echo "<p>None</p>";
					}
					
					// Doc Comments
					$docComment = $method->getDocComment();
					if ($docComment) {
						echo "<p><strong>Doc Comment:</strong></p><pre>" . htmlspecialchars($docComment) . "</pre>";
					}
					
					echo "<hr>";
				}
				
				// Capture the output
				$htmlContent = ob_get_clean();
				
				// Save HTML to file
				$htmlFileName = "{$className}_Class_Methods.html";
				file_put_contents($htmlFileName, $htmlContent);
				
				// Generate PDF
				$pdfFileName = "{$className}_Class_Methods.pdf";
				$dompdf = new Dompdf();
				$dompdf->loadHtml($htmlContent);
				$dompdf->setPaper('A4', 'portrait');
				$dompdf->render();
				file_put_contents($pdfFileName, $dompdf->output());
				
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
