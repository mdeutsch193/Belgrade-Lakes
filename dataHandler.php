<?php 
			date_default_timezone_set('America/New_York');
			set_time_limit(60);
			
			/**
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			**/

			// set up excel reader
			include dirname(__FILE__) . '/Classes/PHPExcel.php';
			include dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';
			PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
    			$objReader = PHPExcel_IOFactory::createReader("Excel2007");
			$objReader->setReadDataOnly(true);
			
			// download realtime data
			file_put_contents('temp/epTempFile.xlsx', fopen("https://www.dropbox.com/s/lzu4pc9tlnf0oix/EPDEP1%202016%20Secchi%20Reading%20Summary%20Graph.xlsx?dl=1", "r"));
			file_put_contents('temp/npTempFile.xlsx', fopen("https://www.dropbox.com/s/vqywig7ws6c8s8k/NPDEP1%202016%20Secchi%20Reading%20Summary%20Graph.xlsx?dl=1", "r"));
			file_put_contents('temp/gpTempFile.xlsx', fopen("https://www.dropbox.com/s/crs3rj59vgagy25/GPDEP2%202016%20%28Goldie%29Secchi%20Reading%20Summary%20Graph.xlsx?dl=1", "r"));
			file_put_contents('temp/lpuTempFile.xlsx', fopen("https://www.dropbox.com/s/s6ih7gb3eo0osyh/LPDEP1%202016%20%28Upper%29%20Secchi%20Reading%20Summary%20Graph.xlsx?dl=1", "r"));
			file_put_contents('temp/lplTempFile.xlsx', fopen("https://www.dropbox.com/s/nfpbfzslwy0qbut/LPDEP2%202016%20%28Lower%29%20Secchi%20Reading%20Summary%20Graph.xlsx?dl=1", "r"));
			file_put_contents('temp/spTempFile.xlsx', fopen("https://www.dropbox.com/s/ka9iq9e8n2d2yu5/MESSDEP1%202016%20%28Sidney%29%20Secchi%20Reading%20Summary%20Graph.xlsx?dl=1", "r"));
			file_put_contents('temp/slTempFile.xlsx', fopen("https://www.dropbox.com/s/d8mlro1aha88ggm/SPDEP1%202016%20Secchi%20Reading%20Summary%20Graph.xlsx?dl=1", "r"));
			
			// load East Pond
    			$objPHPExcel = $objReader->load('temp/epTempFile.xlsx');
			// find last entry
			$sheet = $objPHPExcel->getSheet(1);
			$row = 2;
			$cell = $sheet->getCellByColumnAndRow(3, $row);
			while($cell->getValue() != NULL && $cell->getValue() != '') {
				$row++;
            			$cell = $sheet->getCellByColumnAndRow(3, $row);
    			}
			$eastPond = $sheet->getCellByColumnAndRow(3, $row-1)->getValue();
			$tempDate = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row-1)->getCalculatedValue());
			$epDate = date('m/d/y', $tempDate);
			unlink("temp/epTempFile.xlsx");

			// load North Pond
    			$objPHPExcel = $objReader->load('temp/npTempFile.xlsx');
			// find last entry
			$sheet = $objPHPExcel->getSheet(1);
			$row = 5;
			$cell = $sheet->getCellByColumnAndRow(3, $row);
			while($cell->getValue() != NULL && $cell->getValue() != '') {
				$row++;
            			$cell = $sheet->getCellByColumnAndRow(3, $row);
    			}
			$northPond = $sheet->getCellByColumnAndRow(3, $row-1)->getValue();
			$tempDate = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row-1)->getCalculatedValue());
			$npDate = date('m/d/y', $tempDate);
			unlink("temp/npTempFile.xlsx");

			// load Great Pond
    			$objPHPExcel = $objReader->load('temp/gpTempFile.xlsx');
			// find last entry
			$sheet = $objPHPExcel->getSheet(1);
			$row = 2;
			$cell = $sheet->getCellByColumnAndRow(3, $row);
			while($cell->getValue() != NULL && $cell->getValue() != '') {
				$row++;
            			$cell = $sheet->getCellByColumnAndRow(3, $row);
    			}
			$greatPond = $sheet->getCellByColumnAndRow(3, $row-1)->getValue();
			$tempDate = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row-1)->getCalculatedValue());
			$gpDate = date('m/d/y', $tempDate);
			unlink("temp/gpTempFile.xlsx");

			// load Upper Long Pond
    			$objPHPExcel = $objReader->load('temp/lpuTempFile.xlsx');
			// find last entry
			$sheet = $objPHPExcel->getSheet(1);
			$row = 2;
			$cell = $sheet->getCellByColumnAndRow(3, $row);
			while($cell->getValue() != NULL && $cell->getValue() != '') {
				$row++;
            			$cell = $sheet->getCellByColumnAndRow(3, $row);
    			}
			$upperLongPond = $sheet->getCellByColumnAndRow(3, $row-1)->getValue();
			$tempDate = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row-1)->getCalculatedValue());
			$lpuDate = (string)date('m/d/y', $tempDate);
			unlink("temp/lpuTempFile.xlsx");


			// load Lower Long Pond
    			$objPHPExcel = $objReader->load('temp/lplTempFile.xlsx');
			// find last entry
			$sheet = $objPHPExcel->getSheet(1);
			$row = 4;
			$cell = $sheet->getCellByColumnAndRow(3, $row);
			while($cell->getValue() != NULL && $cell->getValue() != '') {
				$row++;
            			$cell = $sheet->getCellByColumnAndRow(3, $row);
    			}
			$lowerLongPond = $sheet->getCellByColumnAndRow(3, $row-1)->getValue();
			$tempDate = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row-1)->getCalculatedValue());
			$lplDate = date('m/d/y', $tempDate);
			unlink("temp/lplTempFile.xlsx");

			// load Snow Pond
    			$objPHPExcel = $objReader->load('temp/spTempFile.xlsx');
			// find last entry
			$sheet = $objPHPExcel->getSheet(1);
			$row = 4;
			$cell = $sheet->getCellByColumnAndRow(3, $row);
			while($cell->getValue() != NULL && $cell->getValue() != '') {
				$row++;
            			$cell = $sheet->getCellByColumnAndRow(3, $row);
    			}
			$snowPond = $sheet->getCellByColumnAndRow(3, $row-1)->getValue();
			$tempDate = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row-1)->getCalculatedValue());
			$spDate = date('m/d/y', $tempDate);
			unlink("temp/spTempFile.xlsx");

			// load Salmon Lake
    			$objPHPExcel = $objReader->load('temp/slTempFile.xlsx');
			// find last entry
			$sheet = $objPHPExcel->getSheet(1);
			$row = 4;
			$cell = $sheet->getCellByColumnAndRow(3, $row);
			while($cell->getValue() != NULL && $cell->getValue() != '') {
				$row++;
            			$cell = $sheet->getCellByColumnAndRow(3, $row);
    			}
			$salmonLake = $sheet->getCellByColumnAndRow(3, $row-1)->getValue();
			$tempDate = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $row-1)->getCalculatedValue());
			$slDate = date('m/d/y', $tempDate);
			unlink("temp/slTempFile.xlsx");

			// 2015 data
			$eastPondMax = 6.5;
			$eastPondMin = 2;
			$northPondMax = 5.2;
			$northPondMin = 3.5;
			$greatPondMax = 8;
			$greatPondMin = 4.7;
			$upperLongPondMax = 7.5;
			$lowerLongPondMax = 7.5;
			$upperLongPondMin = 5.1;
			$lowerLongPondMin = 5.1;
			$snowPondMax = 6.6;
			$snowPondMin = 4.5;
			$salmonLakeMax = 6.6;
			$salmonLakeMin = 5.1;

			// download DO and temperature data (UPDATE LINKS REGULARLY)
			file_put_contents('temp/epDOTTempFile.xlsx', fopen("https://www.dropbox.com/s/xydkvppy2ino65a/EPDEP1_2016-07-26%20workup.xlsx?dl=1", "r"));
			file_put_contents('temp/npDOTTempFile.xlsx', fopen("https://www.dropbox.com/s/gzu8d9dv8ka13wk/NPDEP1_2016-07-26%20workup%20.xlsx?dl=1", "r"));
			file_put_contents('temp/gpDOTTempFile.xlsx', fopen("https://www.dropbox.com/s/9t2q36unfawxoyi/GPDEP2%202016-07-27workup.xlsx?dl=1", "r"));
			file_put_contents('temp/lpuDOTTempFile.xlsx', fopen("https://www.dropbox.com/s/wjv8c9pdaxm191n/LPDEP1%202016-07-21%20workup.xlsx?dl=1", "r"));
			file_put_contents('temp/lplDOTTempFile.xlsx', fopen("https://www.dropbox.com/s/qn4h6qfhitvb5pp/LPDEP2%202016-07-28%20workup%20.xlsx?dl=1", "r"));
			file_put_contents('temp/spDOTTempFile.xlsx', fopen("https://www.dropbox.com/s/10gbpubb7w0hopd/MESSDEP1_2016-07-29%20workup.xlsx?dl=1", "r"));
			file_put_contents('temp/slDOTTempFile.xlsx', fopen("https://www.dropbox.com/s/ftltr7ia3skf5t2/SPDEP1_2016-07-25%20workup.xlsx?dl=1", "r"));

			// load East Pond
    			$objPHPExcel = $objReader->load('temp/epDOTTempFile.xlsx');
			$sheet = $objPHPExcel->getSheet(1);
			$epT = $sheet->getCellByColumnAndRow(16, 2)->getCalculatedValue();
			$epDO = $sheet->getCellByColumnAndRow(19, 2)->getCalculatedValue();
			unlink("temp/epDOTTempFile.xlsx");

			// load North Pond
    			$objPHPExcel = $objReader->load('temp/npDOTTempFile.xlsx');
			$sheet = $objPHPExcel->getSheet(1);
			$npT = $sheet->getCellByColumnAndRow(16, 2)->getCalculatedValue();
			$npDO = $sheet->getCellByColumnAndRow(19, 2)->getCalculatedValue();
			unlink("temp/npDOTTempFile.xlsx");

			// load Great Pond
    			$objPHPExcel = $objReader->load('temp/gpDOTTempFile.xlsx');
			$sheet = $objPHPExcel->getSheet(1);
			$gpT = $sheet->getCellByColumnAndRow(16, 2)->getCalculatedValue();
			$gpDO = $sheet->getCellByColumnAndRow(19, 2)->getCalculatedValue();
			unlink("temp/gpDOTTempFile.xlsx");

			// load Upper Long Pond
    			$objPHPExcel = $objReader->load('temp/lpuDOTTempFile.xlsx');
			$sheet = $objPHPExcel->getSheet(1);
			$lpuT = $sheet->getCellByColumnAndRow(16, 2)->getCalculatedValue();
			$lpuDO = $sheet->getCellByColumnAndRow(19, 2)->getCalculatedValue();
			unlink("temp/lpuDOTTempFile.xlsx");

			// load Lower Long Pond
    			$objPHPExcel = $objReader->load('temp/lplDOTTempFile.xlsx');
			$sheet = $objPHPExcel->getSheet(1);
			$lplT = $sheet->getCellByColumnAndRow(16, 2)->getCalculatedValue();
			$lplDO = $sheet->getCellByColumnAndRow(19, 2)->getCalculatedValue();
			unlink("temp/lplDOTTempFile.xlsx");

			// load Snow Pond
    			$objPHPExcel = $objReader->load('temp/spDOTTempFile.xlsx');
			$sheet = $objPHPExcel->getSheet(1);
			$spT = $sheet->getCellByColumnAndRow(16, 2)->getCalculatedValue();
			$spDO = $sheet->getCellByColumnAndRow(19, 2)->getCalculatedValue();
			unlink("temp/spDOTTempFile.xlsx");

			// load Salmon Lake
    			$objPHPExcel = $objReader->load('temp/slDOTTempFile.xlsx');
			$sheet = $objPHPExcel->getSheet(1);
			$slT = $sheet->getCellByColumnAndRow(16, 2)->getCalculatedValue();
			$slDO = $sheet->getCellByColumnAndRow(19, 2)->getCalculatedValue();
			unlink("temp/slDOTTempFile.xlsx");
?>
