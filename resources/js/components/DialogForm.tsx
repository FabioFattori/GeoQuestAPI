

import * as React from 'react';
import Dialog from '@mui/material/Dialog';
import DialogContent from '@mui/material/DialogContent';
import DialogTitle from '@mui/material/DialogTitle';
import {Form} from './Form';

export default function DialogForm({open,openDialog,closeDialog,headers=[],fieldNames=[], data=[]}:{open:boolean,openDialog:()=>void,closeDialog:()=>void,headers: string[],fieldNames:string[] , data: any[]}) {
  

  return (
    <React.Fragment>
      <Dialog
        style={{ width: '100%' }}
        open={open}
        onClose={closeDialog}
        PaperProps={{
          component: 'form',
          onSubmit: (event: React.FormEvent<HTMLFormElement>) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);
            const formJson = Object.fromEntries((formData as any).entries());
            const email = formJson.email;
            closeDialog();
          },
        }}
      >
        <DialogTitle>{data.length != 0 ? "Modifica" : "Aggiungi"}</DialogTitle>
        <DialogContent style={{ width: '100%' }}>
          <Form headers={headers} fieldNames={fieldNames} data={data} />
        </DialogContent>
      </Dialog>
    </React.Fragment>
  );
}