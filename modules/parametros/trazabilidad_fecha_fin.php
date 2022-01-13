<div class="col-sm-3 form-group">
    <label for="fechaInicial">Fecha Final</label>  
    <div class="row compositeDate">
        <div class="col-sm-4 form-group nopadding">
            <select name="annof" id="annof" class="form-control">
                <option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
          </select>
        </div>
        <div class="col-sm-5 form-group nopadding">
            <input type="text" name="mesfText" id="mesfText" value="mm" readonly="readonly" class="form-control">
            <input type="hidden" name="mesf" id="mesf" value="">
        </div>
        <div class="col-sm-3 form-group nopadding">
            <select name="diaf" id="diaf" class="form-control">
                           <option value="">dd</option>


                           <option value="1" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 1) {echo " selected "; } ?>>01</option>


                           <option value="2" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 2) {
                              echo " selected ";
                           } ?>>02</option>
                           <option value="3" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 3) {
                              echo " selected ";
                           } ?>>03</option>
                           <option value="4" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 4) {
                              echo " selected ";
                           } ?>>04</option>
                           <option value="5" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 5) {
                              echo " selected ";
                           } ?>>05</option>
                           <option value="6" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 6) {
                              echo " selected ";
                           } ?>>06</option>
                           <option value="7" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 7) {
                              echo " selected ";
                           } ?>>07</option>
                           <option value="8" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 8) {
                              echo " selected ";
                           } ?>>08</option>
                           <option value="9" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 9) {
                              echo " selected ";
                           } ?>>09</option>
                           <option value="10" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 10) {
                              echo " selected ";
                           } ?>>10</option>
                           <option value="11" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 11) {
                              echo " selected ";
                           } ?>>11</option>
                           <option value="12" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 12) {
                              echo " selected ";
                           } ?>>12</option>
                           <option value="13" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 13) {
                              echo " selected ";
                           } ?>>13</option>
                           <option value="14" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 14) {
                              echo " selected ";
                           } ?>>14</option>
                           <option value="15" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 15) {
                              echo " selected ";
                           } ?>>15</option>
                           <option value="16" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 16) {
                              echo " selected ";
                           } ?>>16</option>
                           <option value="17" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 17) {
                              echo " selected ";
                           } ?>>17</option>
                           <option value="18" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 18) {
                              echo " selected ";
                           } ?>>18</option>
                           <option value="19" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 19) {
                              echo " selected ";
                           } ?>>19</option>
                           <option value="20" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 20) {
                              echo " selected ";
                           } ?>>20</option>
                           <option value="21" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 21) {
                              echo " selected ";
                           } ?>>21</option>
                           <option value="22" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 22) {
                              echo " selected ";
                           } ?>>22</option>
                           <option value="23" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 23) {
                              echo " selected ";
                           } ?>>23</option>
                           <option value="24" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 24) {
                              echo " selected ";
                           } ?>>24</option>
                           <option value="25" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 25) {
                              echo " selected ";
                           } ?>>25</option>
                           <option value="26" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 26) {
                              echo " selected ";
                           } ?>>26</option>
                           <option value="27" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 27) {
                              echo " selected ";
                           } ?>>27</option>
                           <option value="28" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 28) {
                              echo " selected ";
                           } ?>>28</option>
                           <option value="29" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 29) {
                              echo " selected ";
                           } ?>>29</option>
                           <option value="30" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 30) {
                              echo " selected ";
                           } ?>>30</option>
                           <option value="31" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 31) {
                              echo " selected ";
                           } ?>>31</option>
                       </select>
        </div>
    </div>
</div><!-- /Fecha final -->