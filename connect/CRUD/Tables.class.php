<?php 
    require_once("CRUD.class.php");

    class Person  Extends Crud {		
        # Your Table name 
        protected $table = 'persons';

        # Primary Key of the Table
        protected $pk	 = 'id';
    }
    
    class User  Extends Crud {		
        # Your Table name 
        protected $table = 'user';

        # Primary Key of the Table
        protected $pk	 = 'id';
    }
    
    class Product  Extends Crud {		
        # Your Table name 
        protected $table = 'products';

        # Primary Key of the Table
        protected $pk	 = 'id';
    }
    
    class Variant  Extends Crud {		
        # Your Table name 
        protected $table = 'product_variants';

        # Primary Key of the Table
        protected $pk	 = 'id';
    }
    
    class Finish  Extends Crud {		
        # Your Table name 
        protected $table = 'product_finish';

        # Primary Key of the Table
        protected $pk	 = 'id';
    }
    
    class FinishSlider  Extends Crud {		
        # Your Table name 
        protected $table = 'product_finish_slider';

        # Primary Key of the Table
        protected $pk	 = 'id';
    }
?>