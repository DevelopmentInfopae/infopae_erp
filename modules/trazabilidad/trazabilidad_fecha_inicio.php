<div class="col-sm-3 form-group">
                                    <label for="fechaInicial">Fecha Inicial</label>  
                                    <div class="row compositeDate">
                                        <div class="col-sm-4 nopadding"> 
                                            <select name="annoi" id="annoi" class="form-control">
                                                <option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
                                            </select>  
                                        </div><!-- /.col-sm-4 -->   
                                        <div class="col-sm-5 nopadding">
                                            <?php
                                                if(!isset($_GET['pb_mesi']) || $_GET['pb_mesi'] == ''){
                                                    $_GET['pb_mesi'] = date("n");
                                                }
                                            ?>
                                            <select name="mesi" id="mesi" onchange="mesFinal();" class="form-control">
                                                <option value="">mm</option>
                                                <option value="1" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 1) {echo " selected "; } ?>>Enero</option>
                                                <option value="2" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 2) {echo " selected "; } ?>>Febrero</option>
                                                <option value="3" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 3) {echo " selected "; } ?>>Marzo</option>
                                                <option value="4" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 4) {echo " selected "; } ?>>Abril</option>
                                                <option value="5" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 5) {echo " selected "; } ?>>Mayo</option>
                                                <option value="6" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 6) {echo " selected "; } ?>>Junio</option>
                                                <option value="7" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 7) {echo " selected "; } ?>>Julio</option>
                                                <option value="8" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 8) {echo " selected "; } ?>>Agosto</option>
                                                <option value="9" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 9) {echo " selected "; } ?>>Septiembre</option>
                                                <option value="10" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 10) {echo " selected "; } ?>>Octubre</option>
                                                <option value="11" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 11) {echo " selected "; } ?>>Noviembre</option>
                                                <option value="12" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 12) {echo " selected "; } ?>>Diciembre</option>
                                            </select>
                                            <input type="hidden" name="mesiConsulta" id="mesiConsulta" value="<?php if (isset($_GET['pb_mesi'])) { echo $_GET['pb_mesi']; } ?>">
                                        </div><!-- /.col --> 


                                        <div class="col-md-3 nopadding"> 





   
                                            <select name="diai" id="diai" class="form-control">
                           <option value="">dd</option>


                           <option value="1" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 1) {echo " selected "; } ?>>01</option>


                           <option value="2" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 2) {
                              echo " selected ";
                           } ?>>02</option>
                           <option value="3" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 3) {
                              echo " selected ";
                           } ?>>03</option>
                           <option value="4" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 4) {
                              echo " selected ";
                           } ?>>04</option>
                           <option value="5" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 5) {
                              echo " selected ";
                           } ?>>05</option>
                           <option value="6" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 6) {
                              echo " selected ";
                           } ?>>06</option>
                           <option value="7" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 7) {
                              echo " selected ";
                           } ?>>07</option>
                           <option value="8" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 8) {
                              echo " selected ";
                           } ?>>08</option>
                           <option value="9" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 9) {
                              echo " selected ";
                           } ?>>09</option>
                           <option value="10" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 10) {
                              echo " selected ";
                           } ?>>10</option>
                           <option value="11" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 11) {
                              echo " selected ";
                           } ?>>11</option>
                           <option value="12" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 12) {
                              echo " selected ";
                           } ?>>12</option>
                           <option value="13" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 13) {
                              echo " selected ";
                           } ?>>13</option>
                           <option value="14" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 14) {
                              echo " selected ";
                           } ?>>14</option>
                           <option value="15" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 15) {
                              echo " selected ";
                           } ?>>15</option>
                           <option value="16" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 16) {
                              echo " selected ";
                           } ?>>16</option>
                           <option value="17" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 17) {
                              echo " selected ";
                           } ?>>17</option>
                           <option value="18" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 18) {
                              echo " selected ";
                           } ?>>18</option>
                           <option value="19" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 19) {
                              echo " selected ";
                           } ?>>19</option>
                           <option value="20" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 20) {
                              echo " selected ";
                           } ?>>20</option>
                           <option value="21" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 21) {
                              echo " selected ";
                           } ?>>21</option>
                           <option value="22" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 22) {
                              echo " selected ";
                           } ?>>22</option>
                           <option value="23" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 23) {
                              echo " selected ";
                           } ?>>23</option>
                           <option value="24" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 24) {
                              echo " selected ";
                           } ?>>24</option>
                           <option value="25" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 25) {
                              echo " selected ";
                           } ?>>25</option>
                           <option value="26" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 26) {
                              echo " selected ";
                           } ?>>26</option>
                           <option value="27" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 27) {
                              echo " selected ";
                           } ?>>27</option>
                           <option value="28" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 28) {
                              echo " selected ";
                           } ?>>28</option>
                           <option value="29" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 29) {
                              echo " selected ";
                           } ?>>29</option>
                           <option value="30" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 30) {
                              echo " selected ";
                           } ?>>30</option>
                           <option value="31" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 31) {
                              echo " selected ";
                           } ?>>31</option>
                       </select>  
                                            </div><!-- /.col-sm-4 --> 
                                        </div><!-- /.row -->                          
                                    </div><!-- /Fecha inicial -->