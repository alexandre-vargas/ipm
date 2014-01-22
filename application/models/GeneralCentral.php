<?php
class GeneralCentral {
	public static function convertWhere($arrWhere = array()) {
		if(!count($arrWhere))
			return;
		$objZendDbSelect = new Zend_Db_Select(Zend_Registry::get('db')); // Envia o adaptador armazenado no Registry apenas para conseguir instanciar Zend_Db_Select
		foreach ($arrWhere as $key => $val)
			$objZendDbSelect->where($key, $val);
		$strWhere = '';
		foreach($objZendDbSelect->getPart('where') as $strVal)
			$strWhere .= $strVal . ' ';
	
		return ' WHERE ' . $strWhere;
	}
	
	public static function pagination1($intPage, $intQtdPages) {
		$objZendViewHelperUrl = new Zend_View_Helper_Url();
		?>
		<div class="pagination">
			<ul>
			
				<?php
				if($intPage == 1)
					print '<li class="disabled"> <a> Anterior </a> </li>';
				else
					print '<li> <a href="' . $objZendViewHelperUrl->url(array('action' => 'listagem', 'page' => $intPage -1)) . '"> Anterior </a> </li>';
				?>
								
				<?php 
				for($i=1; $i <= $intQtdPages; $i++)
					if($i == $intPage)
						print '<li class="active"><a>' . $i . '</a></li>';
					else 
						print '<li><a href="' . $objZendViewHelperUrl->url(array('action' => 'listagem', 'page' => $i)) . '">' . $i . '</a></li>';
				?>
				
				<?php
				if($intPage == $intQtdPages)
					print '<li class="disabled"> <a> Próximo </a> </li>';
				else
					print '<li> <a href="' . $objZendViewHelperUrl->url(array('action' => 'listagem', 'page' => $intPage + 1)) . '"> Próxima </a> </li>';
				?>
				
			</ul>
		</div>
		<?php		
	}
	
	public static function pagination2($intPage, $intQtdPages, $intQtdPerPage, $intQtdTotal) {
		$objZendViewHelperUrl = new Zend_View_Helper_Url();
		?>
		<table class="table datagrid datagrid-stretch-footer">
			<tfoot>
				<tr>
					<th colspan="4">
						<div class="datagrid-footer-left" style="visibility: visible;">
							<div class="grid-controls">
								<span> <span class="grid-start"><?= ($intQtdTotal == 0 ? 0 : 1) ?></span> - <span class="grid-end"><?= ($intQtdTotal < $intQtdPerPage ? $intQtdTotal : $intQtdPerPage) ?></span>
									de <span class="grid-count"> <?= $intQtdTotal ?> items</span>
								</span>
								
								<div class="input-append dropdown combobox">
									<input class="span1" type="text" value="<?= ($intQtdTotal == 0 ? '---' : $intQtdPerPage) ?>" <?= ($intQtdTotal == 0 ? 'disabled' : '') ?>>
									<button class="btn" data-toggle="dropdown" <?= ($intQtdTotal == 0 ? 'disabled' : '') ?>>
										<i class="caret"></i>
									</button>
									<ul class="dropdown-menu">
									 	<!-- DELETAR -->
										<li> <a style="cursor: pointer;" onClick="location.href= '<?= $objZendViewHelperUrl->url(array('action' => 'listagem', 'qtdPerPage' => 5)); ?>'">5</a></li>
										<li> <a style="cursor: pointer;" onClick="location.href= '<?= $objZendViewHelperUrl->url(array('action' => 'listagem', 'qtdPerPage' => 10)); ?>'">10</a></li>
										<li> <a style="cursor: pointer;" onClick="location.href= '<?= $objZendViewHelperUrl->url(array('action' => 'listagem', 'qtdPerPage' => 15)); ?>'">15</a></li>
										<li> <a style="cursor: pointer;" onClick="location.href= '<?= $objZendViewHelperUrl->url(array('action' => 'listagem', 'qtdPerPage' => 20)); ?>'">20</a></li>
										<li> <a style="cursor: pointer;" onClick="location.href= '<?= $objZendViewHelperUrl->url(array('action' => 'listagem', 'qtdPerPage' => 30)); ?>'">30</a></li>

									</ul>
								</div>
								
								<span>por página</span>
							</div>
						</div>
						<div class="datagrid-footer-right" style="visibility: visible;">
							<div class="grid-pager">
								<button 
									type="button" 
									class="btn grid-prevpage" 
									onClick="location.href= '<?=$objZendViewHelperUrl->url(array('action' => 'listagem', 'page' => $intPage - 1)) ?>' "  
									<?= ($intPage == 1 || !(boolean)$intQtdTotal ? 'disabled="disabled"' : '') ?>
								>
									<i class="icon-chevron-left"></i>
								</button>
								<span>página</span>
								
								<div class="input-append dropdown combobox">
									<input class="span1" type="text" value="<?= ($intQtdTotal == 0 ? '---' : $intPage) ?>" <?= ($intQtdTotal == 0 ? 'disabled' : '') ?>>
									<button class="btn" data-toggle="dropdown" <?= ($intQtdTotal == 0 ? 'disabled' : '') ?>>
										<i class="caret"></i>
									</button>
									<ul class="dropdown-menu">
										<?php 
										for($i=1; $i <= $intQtdPages; $i++)
											print '<li> <a style="cursor: pointer;" onClick="location.href= \'' . $objZendViewHelperUrl->url(array('action' => 'listagem', 'page' => $i)) . '\'">' . $i . '</a></li>'
										?>
									</ul>
								</div>
								
								<span>de <span class="grid-pages"><?= $intQtdPages ?></span>
								</span>
								<button 
									type="button" 
									class="btn grid-nextpage" 
									onClick="location.href= '<?=$objZendViewHelperUrl->url(array('action' => 'listagem', 'page' => $intPage + 1)) ?>' "  
									<?= ($intPage == $intQtdPages || !(boolean)$intQtdTotal ? 'disabled="disabled"' : '') ?>
								>
									<i class="icon-chevron-right"></i>
								</button>
								
							</div>
						</div>
					</th>
				</tr>
			</tfoot>
		</table>
	<?php
	}
	
}
