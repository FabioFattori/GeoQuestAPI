
import Delete from '@mui/icons-material/Delete';
import AddIcon from '@mui/icons-material/Add';
import  Edit  from "@mui/icons-material/Edit";

interface Button {
    label: string;
    icon: any;
    url?: string | null;

}

interface MethodButton extends Button {
    method: ({props}:{props:any}) => void;
}

let buttons = [{ label:"Add", icon: AddIcon, url: null },{label:"Edit", icon: Edit, url: null },{label:"Delete", icon: Delete, url: window.location.href+"/Delete"}] as Button[];

const setUp = (addUrl?:string , editUrl?:string , deleteUrl?:string ) =>{
    buttons = [{ label:"Add", icon: AddIcon, url: addUrl },{label:"Edit", icon: Edit, url: editUrl },{label:"Delete", icon: Delete, url: deleteUrl}];
}

const setTableToUse = (tableName:string) => {
    buttons[2].url = "/admin/"+tableName+"/Delete";
}

const addNewInterractableButton = (label:string, icon:any, method:({props}:{props:any}) => void) => {
    let toAdd:MethodButton = {label:label, icon:icon, method:method};
    if(buttons.filter((button) => button.label == label).length == 0){
        buttons.push(toAdd);
    }
}

const resetButtonsConfiguration = () => {
    while(buttons.length > 3){
        buttons.pop();
    }
}



export type { Button, MethodButton };
export { buttons, setUp , setTableToUse,addNewInterractableButton,resetButtonsConfiguration};