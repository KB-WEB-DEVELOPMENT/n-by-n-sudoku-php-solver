<?php 

  class Sudoku {
 
    private $_matrix;
    private $_size;
 
    public function __construct(int $_size = null, array $matrix = null) {
        
      $this->_size = (int)$_size;
		
      if (!isset($matrix)) {
			              
        $this->_matrix = $this->_getEmptyMatrix();
        } else {
          $this->_matrix = $matrix;
        }
      }
 
    public function generate() {
								
      $this->_matrix = $this->_solve($this->_getEmptyMatrix());
		
      $cells = array_rand(range(0,$this->_size*$this->_size-1), $this->_size*$this->_size);

      $i = 0;
		
      foreach ($this->_matrix as &$row) {
			
      foreach ($row as &$cell) {
            
        if (!in_array($i++, $cells)) {
            $cell = null;
        }
      }
      return $this->_matrix;
      }
    }
	
    public function solve() {
							
      $this->_matrix = $this->_solve($this->_matrix);
      return $this->_matrix;
    }
 
    public function getHtml() {
		
        echo '<table border="1"><tbody>';
		
			for ($row = 0; $row < $this->_size; $row++) {     
			
				echo '<tr>';
			
					for ($column = 0; $column < $this->_size; $column++) { 			       
						echo '<td>' . $this->_matrix[$row][$column] . '</td>';
					}
				echo '</tr>'; 
			}
		echo '</tbody></table>';
	}

    private function _getEmptyMatrix() {
	
		 return array_fill(0, $this->_size, array_fill(0, $this->_size, 0));
    }
 
    private function _solve($matrix) {
		
        while(true) {
			
            $options = array();
			
            foreach ($matrix as $rowIndex => $row) {
				
                foreach ($row as $columnIndex => $cell) {
					
                    if (!empty($cell)) {
                        continue;
                    }
					
                    $permissible = $this->_getPermissible($matrix, $rowIndex, $columnIndex);
                    
					if (count($permissible) == 0) {
                        return false;
                    }
                    $options[] = array(
                        'rowIndex' => $rowIndex,
                        'columnIndex' => $columnIndex,
                        'permissible' => $permissible
                    );
                }
            }
            if (count($options) == 0) {
                return $matrix;
            }

            usort($options, array($this, '_sortOptions'));
 
            if (count($options[0]['permissible']) == 1) {
                $matrix[$options[0]['rowIndex']][$options[0]['columnIndex']] = current($options[0]['permissible']);
                continue;
            }
 
            foreach ($options[0]['permissible'] as $value) {
				
                $tmp = $matrix;
                $tmp[$options[0]['rowIndex']][$options[0]['columnIndex']] = $value;

                if ($result = $this->_solve($tmp)) {
                    return $result;
                }
            }
 
            return false;
        }
    }

	private function _getPermissible($matrix, $rowIndex, $columnIndex) {
	    
		$valid = range(1, $this->_size);

        $invalid = $matrix[$rowIndex];
				
		for ($i = 0; $i < $this->_size; $i++) {

            $invalid[] = $matrix[$i][$columnIndex];		
		}
        		
     $invalid = array_unique(array_merge(
		                          $invalid,
		                          array_slice($matrix[$rowIndex], $columnIndex, $this->_size),
                ));
        
		$valid = array_diff($valid, $invalid);

		shuffle($valid);
		
		return $valid;
    }
 
    private function _sortOptions($a, $b) {
        
		$a = count($a['permissible']);
        $b = count($b['permissible']);
		
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
	
}

$grid = array(
			array(1,0,0,0,0),
			array(0,1,0,0,3),
			array(2,0,3,0,0),
			array(3,0,0,4,0),
			array(4,0,0,0,2)
		);

$s = new Sudoku(5,$grid);  
$s->solve();  
echo $s->getHtml(); 

 ?>
