import * as React from "react";
import { alpha } from "@mui/material/styles";
import Box from "@mui/material/Box";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TablePagination from "@mui/material/TablePagination";
import TableRow from "@mui/material/TableRow";
import TableSortLabel from "@mui/material/TableSortLabel";
import Toolbar from "@mui/material/Toolbar";
import Typography from "@mui/material/Typography";
import Paper from "@mui/material/Paper";
import Checkbox from "@mui/material/Checkbox";
import IconButton from "@mui/material/IconButton";
import Tooltip from "@mui/material/Tooltip";
import Delete from "@mui/icons-material/Delete";
import Edit from "@mui/icons-material/Edit";
import DialogForm from "./DialogForm";
import translator from "../utils/EnemyType";
import { router, usePage } from "@inertiajs/react";
import {
    InputBase,
    InputLabel,
    MenuItem,
    Select,
    TextField,
} from "@mui/material";
import SearchIcon from "@mui/icons-material/Search";

interface EnhancedTableProps {
    numSelected: number;
    onSelectAllClick: (event: React.ChangeEvent<HTMLInputElement>) => void;

    rowCount: number;
}

interface EnhancedTableToolbarProps {
    numSelected: number;
    title: string;
    headers: string[];
    fieldNames: string[];
    columns: object[];
    selected: any[];
    buttons: { label: string; icon: any; url?: string | null ,method : ({props}:{props:any})=>{}}[];
    openCreateDialog?: () => void;
    setSelected: (value: any) => void;
    openEditDialog?: () => void;
    getReqObj: (key: any, value: any) => any;
}

function EnhancedTableToolbar(props: EnhancedTableToolbarProps) {
    const { numSelected } = props;
    const { title } = props;
    const { buttons } = props;
    const { selected } = props;
    const { setSelected } = props;
    const { headers } = props;
    const { fieldNames } = props;
    const { getReqObj } = props;
    const { openCreateDialog } = props;
    const { openEditDialog } = props;

    return (
        <Toolbar
            sx={{
                pl: { sm: 2 },
                pr: { xs: 1, sm: 1 },
                ...(numSelected > 0 && {
                    bgcolor: (theme) =>
                        alpha(
                            theme.palette.primary.main,
                            theme.palette.action.activatedOpacity
                        ),
                }),
            }}
        >
            {numSelected > 0 ? (
                <Typography
                    sx={{ flex: "1 1 100%" }}
                    color="inherit"
                    variant="subtitle1"
                    component="div"
                >
                    {numSelected} selected
                </Typography>
            ) : (
                <Typography
                    sx={{ flex: "1 1 100%" }}
                    variant="h6"
                    id="tableTitle"
                    component="div"
                >
                    {title}
                </Typography>
            )}
            {numSelected == 1 &&
            buttons.filter(
                (btn) => btn.label == "Edit" || btn.label == "Delete" || btn.method != undefined
            ).length != 0 ? (
                <>
                    {buttons.filter(btn => btn.label == "Edit").length != 0 ? (
                        <Tooltip title="Edit">
                        <IconButton
                            onClick={() =>
                                buttons[1].url != null
                                    ? router.post(buttons[1].url)
                                    : openEditDialog != undefined
                                    ? openEditDialog()
                                    : console.log("create")
                            }
                        >
                            <Edit />
                        </IconButton>
                    </Tooltip>
                    ) : null}
                    {buttons.filter((btn) => btn.label == "Delete").length != 0 ? (
                        <Tooltip title="Delete">
                        <IconButton
                            onClick={() =>
                                {
                                    console.log(buttons[2].url)
                                    buttons[2].url != null
                                    ? router.post(buttons[2].url,{"id":selected[0]["id"],"headers":headers})
                                    : null
                                    setSelected([]);
                                }
                            }
                        >
                            <Delete />
                        </IconButton>
                    </Tooltip>):null}
                    {buttons.filter(
                        (button) =>
                            button.label != "Delete" && button.label != "Edit" && button.label != "Add"
                    ).map((button, key) => (
                        <Tooltip title={button.label} key={key}>
                            <IconButton
                                onClick={() =>{
                                    button.method({props:selected})
                                }
                                }
                            >
                                <button.icon />
                            </IconButton>
                        </Tooltip>
                    ))}
                </>
            ) : numSelected > 0 && buttons.length >= 3 ? (
                <Tooltip title="Delete">
                    <IconButton
                        onClick={() =>
                            buttons[2].url != null
                                ? router.post(buttons[2].url,{"id":selected[0]["id"]})
                                : console.log("Delete Clicked")
                        }
                    >
                        <Delete />
                    </IconButton>
                </Tooltip>
            ) : null}

            {buttons
                .filter(
                    (button) =>
                        button.label != "Delete" && button.label != "Edit" && button.method == undefined
                )
                .map((button, key) => (
                    <Tooltip title={button.label} key={key}>
                        <IconButton
                            onClick={() =>
                                button.url != null
                                    ? router.post(button.url)
                                    : openCreateDialog != undefined
                                    ? openCreateDialog()
                                    : console.log("create")
                            }
                        >
                            <button.icon />
                        </IconButton>
                    </Tooltip>
                ))}
        </Toolbar>
    );
}
export default function GeneralTable({
    tableTitle = "Table",
    dbObject = {},
    rootForPagination = window.location.pathname,
    buttons = [],
}: {
    tableTitle: string;
    rootForPagination?: string;
    dbObject: any;
    buttons: { label: string; icon: any; url?: string | null }[];
}) {
    const {
        headers,
        fieldNames,
        data,
        page,
        dataPerPage,
        count,
        columns,
        tableId,
    } = translator({ sennisTable: dbObject });

    const [selected, setSelected] = React.useState<any[]>([]);

    React.useEffect(() => {
        setSelected([]);
        setOpenCreate(false);
        setOpenEdit(false);
    }, [data]);

    function EnhancedTableCell({ columnName }: { columnName: string }) {
        const [order, setOrder] = React.useState<"ASC" | "DESC">("ASC");

        interface Sort {
            columnName: string;
            direction: string;
        }

        const sort = (property: string) => {
            let newOrder = "DESC";
            if (dbObject["sorts"].lenght != 0) {
                newOrder =
                    dbObject["sorts"].map((obj: Sort) => obj["direction"])[0] ==
                    "ASC"
                        ? "DESC"
                        : "ASC";
            }
            router.post(
                rootForPagination,
                getReqObject("sorts", [
                    { columnName: property, direction: newOrder },
                ] as Sort[])
            );
        };
        // check if the column is sortable
        if (columns[fieldNames[headers.indexOf(columnName) as number]]["sortable"]) {
            return (
                <TableCell key={columnName} align={"right"} padding={"normal"}>
                    <TableSortLabel
                        active={
                            dbObject["sorts"].length != 0 &&
                            dbObject["sorts"].map(
                                (obj: Sort) => obj["columnName"]
                            )[0] == fieldNames[headers.indexOf(columnName) as number]
                        }
                        direction={
                            dbObject["sorts"].length != 0 &&
                            dbObject["sorts"].map(
                                (obj: Sort) => obj["direction"]
                            )[0] == "ASC" ? "desc" : "asc"
                        }
                        onClick={() => sort(fieldNames[headers.indexOf(columnName) as number])}
                    >
                        {columnName}
                    </TableSortLabel>
                </TableCell>
            );
        }else{
            return (
                <TableCell key={columnName} align={"right"} padding={"normal"}>
                    {columnName}
                </TableCell>
            );
        }
        
    }

    function EnhancedTableHead(props: EnhancedTableProps) {
        const { onSelectAllClick, numSelected, rowCount } = props;

        return (
            <TableHead>
                <TableRow>
                    <TableCell padding="checkbox">
                        <Checkbox
                            color="primary"
                            indeterminate={
                                numSelected > 0 && numSelected < rowCount
                            }
                            checked={rowCount > 0 && numSelected === rowCount}
                            onChange={onSelectAllClick}
                            inputProps={{
                                "aria-label": "select all desserts",
                            }}
                        />
                    </TableCell>
                    {headers
                        .filter(
                            (head, key) => !columns[fieldNames[key]]["hidden"]
                        )
                        .map((headCell, key) => (
                            <EnhancedTableCell
                                key={key}
                                columnName={headCell}
                            />
                        ))}
                </TableRow>
            </TableHead>
        );
    }

    const handleSelectAllClick = (
        event: React.ChangeEvent<HTMLInputElement>
    ) => {
        if (event.target.checked) {
            setSelected(data);
            return;
        }
        setSelected([]);
    };

    const handleClick = (event: React.MouseEvent<unknown>, id: number) => {
        const newSelected = data[id];

        if (
            selected.filter((value) => value["id"] == newSelected["id"])
                .length == 0
        ) {
            setSelected((oldSelected) => [...oldSelected, newSelected]);
        } else {
            setSelected((oldSelected) =>
                oldSelected.filter((value) => value["id"] != newSelected["id"])
            );
        }
    };

    const getReqObject = (key: any, value: any) => {
        return {
            ...dbObject,
            data: undefined,
            column: undefined,
            count: undefined,
            id: tableId,
            [key]: value,
        };
    };

    const handleChangePage = (event: unknown, newPage: number) => {
        router.post(rootForPagination, getReqObject("page", newPage + 1));
    };

    const handleChangeRowsPerPage = (
        event: React.ChangeEvent<HTMLInputElement>
    ) => {
        router.post(
            rootForPagination,
            getReqObject("perPage", parseInt(event.target.value, 10))
        );
    };

    const isSelected = (row: any) =>
        selected.filter((value) => value["id"] == row["id"]).length != 0;

    // Avoid a layout jump when reaching the last page with empty rows.
    const emptyRows = page > 0 ? Math.max(0, page * dataPerPage - count) : 0;

    const [openCreate, setOpenCreate] = React.useState(false);
    const [openEdit, setOpenEdit] = React.useState(false);

    const [columnName, setColumnName] = React.useState(
        headers.length != 0 &&
            columns != undefined &&
            columns[fieldNames[0]]["searchable"]
            ? headers[0]
            : ""
    );

    interface Filter {
        columnName: string;
        value: string;
    }

    const constructFilter = (columnName: string, value: string) => {
        return {
            columnName: columnName,
            value: value,
        };
    };

    const handleSearch = () => {
        let text = (document.getElementById("searchInput"+tableTitle) as HTMLInputElement)
            .value;
            console.log(text)
        if (columnName != "") {
            let filterObj = getReqObject("filters", [
                constructFilter(
                    fieldNames[headers.indexOf(columnName) as number],
                    text
                ),
            ] as Filter[]);
            router.post(rootForPagination, filterObj);
        } else {
            alert("Seleziona una colonna");
        }
    };

    return (
        <Box sx={{ maxWidth: "99%",minWidth:"400px" }} marginTop={10} marginBottom={10} marginLeft={5} marginRight={5}>
            <div
                className="searchBar"
            >
                <Select
                    style={{ width: "20%", marginRight: "10px" }}
                    labelId="demo-select-small-label"
                    id="demo-select-small"
                    value={columnName}
                    label="colonna"
                    onChange={(event) => {
                        setColumnName(event.target.value);
                    }}
                >
                    {headers
                        .filter(
                            (header, key) =>
                                columns[fieldNames[key]]["searchable"] == true
                        )
                        .map((header, key) => (
                            <MenuItem value={header} key={key}>
                                {header}
                            </MenuItem>
                        ))}
                    <MenuItem value={""} key={"sium"}>
                        {"Seleziona una colonna"}
                    </MenuItem>
                </Select>

                <div  style={{ width: "80%", display:"flex",height:"100%" }}>
                    <InputBase
                        id={"searchInput"+tableTitle}
                        style={{ width: "100%" }}
                        sx={{ ml: 1, flex: 1 }}
                        placeholder={"Cerca tra " + tableTitle}
                        type={
                            (columnName &&
                                columns[
                                    fieldNames[
                                        headers.indexOf(columnName) as number
                                    ]
                                ]["type"] == "integer") ||
                            (columnName &&
                                columns[
                                    fieldNames[
                                        headers.indexOf(columnName) as number
                                    ]
                                ]["type"] == "float")
                                ? "number"
                                : "text"
                        }
                        inputProps={{
                            "aria-label": "Cerca tra " + { tableTitle },
                        }}
                    />
                    <IconButton
                        color="primary"
                        onClick={handleSearch}
                        type="button"
                        sx={{ p: "10px" }}
                        aria-label="search"
                    >
                        <SearchIcon />
                    </IconButton>
                </div>
            </div>

            <Paper sx={{maxWidth: "100%",minWidth:"400px" }}>
                <EnhancedTableToolbar
                    getReqObj={getReqObject}
                    columns={columns}
                    headers={headers}
                    fieldNames={fieldNames}
                    numSelected={selected.length}
                    title={tableTitle}
                    buttons={buttons}
                    selected={selected}
                    setSelected={setSelected}
                    openCreateDialog={() => setOpenCreate(true)}
                    openEditDialog={() => setOpenEdit(true)}
                />
                <TableContainer sx={{ maxWidth: "100%",minWidth:"400px", maxHeight: 440 }}>
                    <Table
                        stickyHeader
                        aria-labelledby="tableTitle"
                        size={"medium"}
                    >
                        <EnhancedTableHead
                            numSelected={selected.length}
                            onSelectAllClick={handleSelectAllClick}
                            rowCount={data.length}
                        />
                        <TableBody>
                            {data.map((row: any, index: number) => {
                                const isItemSelected = isSelected(row);
                                const labelId = `enhanced-table-checkbox-${index}`;

                                return (
                                    <TableRow
                                        hover
                                        onClick={(event) =>
                                            handleClick(event, index)
                                        }
                                        role="checkbox"
                                        aria-checked={isItemSelected}
                                        tabIndex={-1}
                                        key={index}
                                        selected={isItemSelected}
                                        sx={{ cursor: "pointer" }}
                                    >
                                        <TableCell padding="checkbox">
                                            <Checkbox
                                                color="primary"
                                                checked={isItemSelected}
                                                inputProps={{
                                                    "aria-labelledby": labelId,
                                                }}
                                            />
                                        </TableCell>
                                        {fieldNames
                                            .filter(
                                                (field) =>
                                                    !columns[field]["hidden"]
                                            )
                                            .map((header, key) => {
                                                let flag = header.toLowerCase().includes("is".toLowerCase())
                                                if(flag){
                                                    return (
                                                        <TableCell
                                                            key={key}
                                                            align="right"
                                                        >
                                                            {row[header] == 1? "Si" : "No"}
                                                        </TableCell>
                                                    );
                                                }else{

                                                    return (
                                                        <TableCell
                                                            key={key}
                                                            align="right"
                                                        >
                                                            {row[header] != null
                                                                ? row[header]
                                                                : "NULL"}
                                                        </TableCell>
                                                    );
                                                }
                                            })}
                                    </TableRow>
                                );
                            })}
                            {emptyRows > 0 && (
                                <TableRow
                                    style={{
                                        height: 53 * emptyRows,
                                    }}
                                >
                                    <TableCell colSpan={6} />
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </TableContainer>
                <TablePagination
                    rowsPerPageOptions={
                        count > 25 ? [5, 10, 25, count] : [5, 10, count]
                    }
                    component="div"
                    count={count}
                    rowsPerPage={dataPerPage}
                    page={page <= 0 ? 0 : page - 1}
                    onPageChange={handleChangePage}
                    onRowsPerPageChange={handleChangeRowsPerPage}
                />
            </Paper>
            <DialogForm
                open={openCreate}
                openDialog={() => setOpenCreate(true)}
                closeDialog={() => setOpenCreate(false)}
                fieldNames={fieldNames.filter(
                    (field) => columns[field]["isOriginal"]
                )}
                headers={headers.filter(
                    (header, key) => columns[fieldNames[key]]["isOriginal"]
                )}
                
                data={[]}

            />
            <DialogForm
                open={openEdit}
                openDialog={() => setOpenEdit(true)}
                closeDialog={() => setOpenEdit(false)}
                fieldNames={fieldNames.filter(
                    (field) => columns[field]["isOriginal"]
                )}
                headers={headers.filter(
                    (header, key) => columns[fieldNames[key]]["isOriginal"])}
                data={selected[0]}
            />
        </Box>
    );
}