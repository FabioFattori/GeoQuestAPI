import Visibility from '@mui/icons-material/Visibility'
import VisibilityOff from '@mui/icons-material/VisibilityOff'
import IconButton from '@mui/material/IconButton'
import InputAdornment from '@mui/material/InputAdornment'
import TextField from '@mui/material/TextField'
import React from 'react'

function PasswordInput({label="",data="",onChange=()=>{}} : {label:string,data:string,onChange:(e:any)=>void}) {
    const [show,setShow] = React.useState(false)

  return (
    <TextField
    onChange={(e)=>onChange(e)}
    key={"Password"+label}
    required
    type={show ? "text" : "password"}
    id={"outlined-required "+label}
    label={label}
    defaultValue={data != "" ? data : ""}
    InputProps={{
      endAdornment: (
        <InputAdornment position="end">
          <IconButton
            onClick={() => setShow(!show)}
          >
            {show ? <Visibility /> : <VisibilityOff />}
          </IconButton>
        </InputAdornment>
      )
    }}
    />
  )
}

export default PasswordInput