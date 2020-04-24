class ModalManagerService
{
    static openModal(id){
        $("#overlay").show();
        $("#" + id).show();
    }

    static closeModal(id){
        $("#overlay").hide();
        $("#" + id).hide();
    }
}